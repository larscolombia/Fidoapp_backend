<?php

namespace App\Http\Controllers\Auth\API;

use DB;
use Auth;
use Hash;
use App\Models\User;
use App\Models\Wallet;
use App\Trait\UserNotification;
use App\Helpers\Functions;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use App\Models\PermissionRequest;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\LoginResource;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\RegisterResource;
use Illuminate\Support\Facades\Password;
use Modules\Commission\Models\Commission;
use App\Http\Resources\SocialLoginResource;
use Modules\Employee\Models\BranchEmployee;
use App\Http\Controllers\Auth\Trait\AuthTrait;
use Modules\Commission\Models\EmployeeCommission;

class AuthController extends Controller
{
    use AuthTrait;
    use UserNotification;

    public function register(Request $request)
    {
        $user = $this->registerTrait($request);
        $success['token'] = $user->createToken(setting('app_name'))->plainTextToken;
        $success['name'] = $user->name;
        Functions::generateSlugInUser($user);
        $userResource = new RegisterResource($user);
        $this->createWallet($user);
        $this->sendNotificationUser('new_user', $user);
        return $this->sendResponse($userResource, __('messages.register_successfull'));
    }

    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $user = User::where('email', request('email'))->first();
        if ($user == null) {
            return response()->json(['status' => false, 'message' => __('messages.register_before_login')]);
        }
        $isActive = $this->checkService($request);
        if ($isActive == 0) {
            return response()->json(['status' => false, 'message' => __('messages.service_inactive')]);
        }
        $usertype = $user->user_type;

        if ($usertype == "vet" || $usertype == "groomer" || $usertype == "walker" || $usertype == "boarder" || $usertype == "trainer" || $usertype == "day_taker") {

            if ($user->email_verified_at == null) {

                return response()->json(['status' => false, 'message' => __('messages.account_not_verify')]);
            }
        }

        if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
            $user = Auth::user();

            if ($user->is_banned == 1 || $user->status == 0) {
                return response()->json(['status' => false, 'message' => __('messages.login_error')]);
            }

            $user->player_id = $request->input('player_id'); // Store the player_id
            // Almacenar el device_token
            if (!is_null($request->input('device_token'))) {
                $user->device_token = $request->input('device_token'); // Guarda el token del dispositivo
            }

            // Save the user
            $user->save();

            // if (!$user->hasRole('user')) {
            //     return $this->sendError(__('messages.role_not_matched'), ['error' => __('messages.unauthorised')], 200);
            // }
            $user['api_token'] = $user->createToken(setting('app_name'))->plainTextToken;
            $loginResource = new LoginResource($user);
            Log::info($loginResource->toJson());
            $message = __('messages.user_login');

            return $this->sendResponse($loginResource, $message);
        } else {
            return $this->sendError(__('messages.not_matched'), ['error' => __('messages.unauthorised')], 200);
        }
    }

    public function socialLogin(Request $request)
    {

        $input = $request->all();

        if ($input['login_type'] === 'mobile') {
            $user_data = User::where('username', $input['username'])->where('login_type', 'mobile')->first();
        } else {
            $user_data = User::where('email', $input['email'])->first();
        }


        if ($user_data != null) {

            $isActive = $this->checkService($request);
            if ($isActive == 0) {
                return response()->json(['status' => false, 'message' => 'This service is inactive. Please contact your Administration.']);
            }

            $usertype = $user_data->user_type;

            if ($usertype == "vet" || $usertype == "groomer" || $usertype == "walker" || $usertype == "boarder" || $usertype == "trainer" || $usertype == "day_taker") {

                if ($user_data->email_verified_at == null) {

                    return response()->json(['status' => false, 'message' => __('messages.account_not_verify')]);
                }
            }



            if (!isset($user_data->login_type) || $user_data->login_type == '') {
                if ($request->login_type === 'google') {
                    $message = __('validation.unique', ['attribute' => 'email']);
                } else {
                    $message = __('validation.unique', ['attribute' => 'username']);
                }

                return $this->sendError($message, 400);
            }
            $message = __('messages.login_success');
        } else {

            if ($request->login_type === 'google' || $request->login_type === 'apple') {
                $key = 'email';
                $value = $request->email;
            } else {
                $key = 'username';
                $value = $request->username;
            }

            // $trashed_user_data = User::where($key, $value)->whereNotNull('login_type')->withTrashed()->first();

            // if ($trashed_user_data != null && $trashed_user_data->trashed()) {
            //     if ($request->login_type === 'google') {
            //         $message = __('validation.unique', ['attribute' => 'email']);
            //     } else {
            //         $message = __('validation.unique', ['attribute' => 'username']);
            //     }

            //     return $this->sendError($message, 400);
            // }

            if ($request->login_type === 'mobile' && $user_data == null) {
                $otp_response = [
                    'status' => true,
                    'is_user_exist' => false,
                ];

                return $this->sendError($otp_response);
            }

            if ($request->login_type === 'mobile' && $user_data != null) {
                $otp_response = [
                    'status' => true,
                    'is_user_exist' => true,
                ];

                return $this->sendError($otp_response);
            }

            $password = !empty($input['accessToken']) ? $input['accessToken'] : $input['email'];

            $input['user_type'] = $request->user_type;
            $input['display_name'] = $input['first_name'] . ' ' . $input['last_name'];
            $input['password'] = Hash::make($password);
            $input['user_type'] = isset($input['user_type']) ? $input['user_type'] : 'user';
            if (request('player_id') != null) {
                $input['player_id'] = request('player_id');
            }
            // Almacenar el device_token
            if (!is_null(request('device_token'))) {
                $input['device_token'] = request('device_token'); // Guarda el token del dispositivo
            }
            $user = User::create($input);
            Functions::generateSlugInUser($user);
            $usertype = $user->user_type;

            $user->assignRole($usertype);

            $user->save();
            $this->createWallet($user);
            if ($usertype == "vet" || $usertype == "groomer" || $usertype == "walker" || $usertype == "boarder" || $usertype == "trainer" || $usertype == "day_taker") {
                $commission = Commission::first();
                EmployeeCommission::create([
                    'employee_id' => $user->id,
                    'commission_id' => $commission->id,
                ]);

                $branch_data = [
                    'employee_id' => $user->id,
                    'branch_id' => 1,
                ];
                BranchEmployee::create($branch_data);
            }


            \Illuminate\Support\Facades\Artisan::call('view:clear');
            \Illuminate\Support\Facades\Artisan::call('cache:clear');
            \Illuminate\Support\Facades\Artisan::call('route:clear');
            \Illuminate\Support\Facades\Artisan::call('config:clear');
            \Illuminate\Support\Facades\Artisan::call('config:cache');

            // if ($user->hasRole('user')) {
            //     return $this->sendError(__('messages.role_not_matched'), ['error' => __('messages.unauthorised')], 200);
            // }
            if (!empty($input['profile_image'])) {
                $media = $user->addMediaFromUrl($input['profile_image'])->toMediaCollection('profile_image');
                $user->avatar = $media->getUrl();
            }
            $user_data = User::where('id', $user->id)->first();

            $message = trans('messages.save_form', ['form' => $input['user_type']]);

            $usertype = $user_data->user_type;

            if ($usertype == "vet" || $usertype == "groomer" || $usertype == "walker" || $usertype == "boarder" || $usertype == "trainer" || $usertype == "day_taker") {

                if ($user_data->email_verified_at == null) {

                    return response()->json(['status' => false, 'message' => __('messages.account_not_verify')]);
                }
            }
        }

        if (request('player_id') != null) {
            $user_data->player_id = request('player_id');
            $user_data->save();
        }

        if (request('device_token') != null) {
            $user_data->device_token = request('device_token');
            $user_data->save();
        }

        $isActive = $this->checkService($request);
        if ($isActive == 0) {
            return response()->json(['status' => false, 'message' => 'This service is inactive. Please contact your Administration.']);
        }

        $user_data['api_token'] = $user_data->createToken('auth_token')->plainTextToken;

        $socialLogin = new SocialLoginResource($user_data);

        return $this->sendResponse($socialLogin, $message);
    }

    public function logout(Request $request)
    {

        $user = Auth::guard('sanctum')->user();

        if ($request->is('api*')) {
            $user->player_id = null;
            $user->save();

            return response()->json(['status' => true, 'message' => __('messages.user_logout')]);
        }
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $response = Password::sendResetLink(
            $request->only('email')
        );
        $user = User::where('email', $request->email)->first();
        if ($user == null) {
            return $response == Password::RESET_LINK_SENT
                ? response()->json(['message' => __($response), 'status' => true], 200)
                : response()->json(['message' => __($response), 'status' => false], 200);
        }

        return $response == Password::RESET_LINK_SENT
            ? response()->json(['message' => __($response), 'status' => true], 200)
            : response()->json(['message' => __($response), 'status' => false], 200);
    }

    public function changePassword(Request $request)
    {
        $user = \Auth::user();
        $user_id = !empty($request->id) ? $request->id : $user->id;
        $user = User::where('id', $user_id)->first();
        if ($user == '') {
            return response()->json([
                'status' => false,
                'message' => __('messages.user_notfound'),
            ], 400);
        }

        $hashedPassword = $user->password;

        $match = Hash::check($request->old_password, $hashedPassword);

        $same_exits = Hash::check($request->new_password, $hashedPassword);

        if ($match) {
            if ($same_exits) {
                $message = __('messages.old_new_pass_same');

                return response()->json([
                    'status' => false,
                    'message' => __('messages.same_pass'),
                ], 400);
            }

            $user->fill([
                'password' => Hash::make($request->new_password),
            ])->save();

            $success['api_token'] = $user->createToken(setting('app_name'))->plainTextToken;
            $success['name'] = $user->name;

            return response()->json([
                'status' => true,
                'data' => $success,
                'message' => __('messages.pass_successfull'),
            ], 200);
        } else {
            $success['api_token'] = $user->createToken(setting('app_name'))->plainTextToken;
            $success['name'] = $user->name;
            $message = __('messages.valid_password');

            return response()->json([
                'status' => true,
                'data' => $success,
                'message' => __('messages.pass_successfull'),
            ], 200);
        }
    }

    public function updateProfile(Request $request)
    {
        try {
            $request->validate([
                'tags' => 'nullable',  // Validación para que sea un JSON
                'pdf' => 'nullable|file|mimes:pdf',  // Validación para que sea un PDF
                'professional_title' => 'nullable|string|max:255',
                'validation_number' => 'nullable|string|max:255',
                'speciality_id' => 'nullable|exists:specialities,id',
            ]);

            $user = \Auth::user();
            if ($request->has('id') && !empty($request->id)) {
                $user = User::where('id', $request->id)->first();
            }

            if ($user == null) {
                return response()->json([
                    'message' => __('messages.no_record'),
                ], 400);
            }

            $user->fill($request->all())->update();

            $user_data = User::find($user->id);
            if ($request->has('profile_image')) {
                storeMediaFile($user_data, $request->file('profile_image'), 'profile_image');
            }

            $user_profile = UserProfile::where('user_id', $user->id)->first();
            if (!$user_profile) {
                $user_profile = new UserProfile();
                $user_profile->user_id = $user->id;
            }

            if ($request->has('expert')) {
                $user_profile->expert = $request->expert;
            }
            if ($request->has('description')) {
                $user_profile->description = $request->description;
            }
            if ($request->has('about_self')) {
                $user_profile->about_self = $request->about_self;
            }
            if ($request->has('facebook_link')) {
                $user_profile->facebook_link = $request->facebook_link;
            }
            if ($request->has('instagram_link')) {
                $user_profile->instagram_link = $request->instagram_link;
            }
            if ($request->has('twitter_link')) {
                $user_profile->twitter_link = $request->twitter_link;
            }
            if ($request->has('dribbble_link')) {
                $user_profile->dribbble_link = $request->dribbble_link;
            }

            if ($request->has('speciality_id')) {
                $user_profile->speciality_id = $request->speciality_id;
            }

            if ($request->has('tags')) {
                // Limpiar la cadena de tags

                $tagsString = $request->tags;
                $cleanedTagsString = str_replace('\"', '', $tagsString);
                $cleanedTagsString = str_replace('\\', '', $cleanedTagsString);
                $cleanedTagsString = str_replace('[', '', $cleanedTagsString);
                $cleanedTagsString = str_replace(']', '', $cleanedTagsString);
                $cleanedTagsString = str_replace("\"", '', $cleanedTagsString);
                $user_profile->tags = $cleanedTagsString;
            }

            if ($request->has('professional_title')) {
                $user_profile->professional_title = $request->professional_title;
            }

            if ($request->has('validation_number')) {
                $user_profile->validation_number = $request->validation_number;
            }

            if (!file_exists(public_path('files/user_profiles'))) {
                mkdir(public_path('files/user_profiles'), 0755, true);
            }

            if ($request->hasFile('pdf')) {
                $file = $request->file('pdf');
                $fileName = time() . '.' . pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
                // Sanitización del nombre del archivo
                $fileName = str_replace([' ', '%'], '-', $fileName);

                // Movimiento del archivo
                $filePath = public_path('files/user_profiles/' . $fileName);
                $file->move(public_path('files/user_profiles'), $fileName);

                // Asignación de la ruta al perfil del usuario
                $user_profile->pdf = 'files/user_profiles/' . $fileName;
            }

            if ($user_profile != '') {
                $user_profile->save();
            }

            // Guardar los datos del usuario
            $user_data = User::find($user->id);
            if ($user_data) {
                $user_data['user_role'] = $user->getRoleNames();
                $user_data['profile_image'] = $user->profile_image;
                $user_data['about_self'] = $user->profile->about_self ?? null;
                $user_data['expert'] = $user->profile->expert ?? null;
                $user_data['facebook_link'] = $user->profile->facebook_link ?? null;
                $user_data['instagram_link'] = $user->profile->instagram_link ?? null;
                $user_data['twitter_link'] = $user->profile->twitter_link ?? null;
                $user_data['dribbble_link'] = $user->profile->dribbble_link ?? null;
                $user_data['raiting'] = $user->raiting ?? null;
                $user_data['tags'] = !is_null($user->profile) && !is_null($user->profile->tags)  ? explode(',', $user->profile->tags) : [];
                $user_data['professional_title'] = $user->profile->professional_title ?? null;
                $user_data['validation_number'] = $user->profile->validation_number ?? null;
                $user_data['pdf'] = !is_null($user->profile) && !is_null($user->profile->pdf) ? asset($user->profile->pdf) : null;
                $user_data['speciality_id'] = !is_null($user->profile) && !is_null($user->profile->speciality) ? $user->profile->speciality->description :  null;
                // Campos adicionales que faltaban
                $user_data['id'] = $user->id;
                $user_data['first_name'] = $user->first_name;
                $user_data['last_name'] = $user->last_name;
                $user_data['mobile'] = $user->mobile;
                $user_data['email'] = $user->email;
                $user_data['api_token'] = $user->api_token;
                $user_data['user_type'] = $user->user_type;
                $user_data['login_type'] = $user->login_type;
                $user_data['gender'] = $user->gender;
                $user_data['address'] = $user->address;
                $user_data['player_id'] = $user->player_id;
                $user_data['device_token'] = $user->device_token;

                unset($user_data['roles']);
                unset($user_data['media']);

                return response()->json([
                    'status' => true,
                    'data' => $user_data,
                    'message' => __('messages.profile_update'),
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => __('Error') . ': ' . $e->getMessage(),
            ], 500);
        }
    }

    public function userDetails(Request $request)
    {
        $userID = $request->id;
        $user = User::find($userID);
        $user['about_self'] = $user->profile->about_self ?? null;
        $user['expert'] = $user->profile->expert ?? null;
        $user['facebook_link'] = $user->profile->facebook_link ?? null;
        $user['instagram_link'] = $user->profile->instagram_link ?? null;
        $user['twitter_link'] = $user->profile->twitter_link ?? null;
        $user['dribbble_link'] = $user->profile->dribbble_link ?? null;
        $user['raiting'] = $user->raiting ?? null;
        $user['tags'] = !is_null($user->profile) && !is_null($user->profile->tags)  ? explode(',', $user->profile->tags) : [];
        $user['professional_title'] = $user->profile->professional_title ?? null;
        $user['validation_number'] = $user->profile->validation_number ?? null;
        $user['pdf'] = !is_null($user->profile) && !is_null($user->profile->pdf) ? asset($user->profile->pdf) : null;
        $user['speciality_id'] =  !is_null($user->profile) && !is_null($user->profile->speciality) ? $user->profile->speciality->description :  null;
        if (!$user) {
            return response()->json(['status' => false, 'message' => __('messages.user_notfound')], 404);
        }

        return response()->json(['status' => true, 'data' => $user, 'message' => __('messages.user_details_successfull')]);
    }

    public function deleteAccount(Request $request)
    {
        $user_id = \Auth::user()->id;
        $user = User::where('id', $user_id)->first();
        if ($user == null) {
            $message = __('messages.user_not_found');

            return response()->json([
                'status' => false,
                'message' => $message,
            ], 200);
        }
        $user->booking()->forceDelete();
        $user->forceDelete();
        $message = __('messages.delete_account');

        return response()->json([
            'status' => true,
            'message' => $message,
        ], 200);
    }

    public function requestPermission(Request $request)
    {
        try {
            $data = $request->validate([
                'trainer_id' => 'required|exists:users,id',
                'user_id' => 'required|exists:users,id'
            ]);
            $permissionRequest = new PermissionRequest();
            $permissionRequest->requester_id = $data['trainer_id'];
            $permissionRequest->target_id = $data['user_id'];
            $permissionRequest->save();

            return response()->json([
                'status' => true,
                'message' => 'Request created',
                'data' => $permissionRequest,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function respondToRequest(Request $request, $requestId)
    {
        try {
            $permissionRequest = PermissionRequest::findOrFail($requestId);

            if ($request->input('response') == 'accept') {
                $permissionRequest->accepted = true;
            } else {
                $permissionRequest->accepted = false;
            }

            $permissionRequest->save();

            return response()->json([
                'success' => true,
                'message' => 'Request updated',
                'data' => $permissionRequest
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function requestPermissionForUser(Request $request)
    {
        try {
            $data = $request->validate([
                'user_id' => 'required|exists:users,id',
                'accepted' => 'nullable|boolean'
            ]);
            $accepted = (isset($data['accepted']) && !is_null($data['accepted'])) ? $data['accepted'] : null;
            $permissionRequest = PermissionRequest::where('user_id', $data['user_id'])
                ->where('accepted', $accepted)
                ->get();
            return response()->json([
                'success' => true,
                'data' => $permissionRequest
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    private function createWallet($user)
    {
        Wallet::firstOrCreate([
            'user_id' => $user->id,
        ], [
            'balance' => 0, // Puedes establecer un saldo inicial si lo deseas
        ]);
    }

    public function updateDeviceToken(Request $request)
    {
        // Validar los datos de entrada
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'device_token' => ['required', 'string']
        ]);

        // Actualizar el token del dispositivo
        $userUpdated = User::where('id', $data['user_id'])->update(['device_token' => $data['device_token']]);

        // Verificar si la actualización fue exitosa
        if ($userUpdated) {
            return response()->json(['success' => true, 'data' => $data], 200);
        }

        // Manejo de errores si no se actualizó el usuario
        return response()->json(['success' => false, 'message' => 'No se pudo actualizar el token del dispositivo'], 500);
    }
}
