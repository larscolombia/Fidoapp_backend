<?php

namespace Database\Seeders;

use App\Models\ExpiryDate;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Filesystem\Filesystem;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        $file = new Filesystem;
        $file->cleanDirectory('storage/app/public');
        Setting::create(['name' => 'slot_duration', 'val' => '00:15', 'type' => 'text']);
        $this->call(BranchSeeder::class);
        $this->call(AuthTableSeeder::class);
        $this->call(ModulesSeeder::class);
        $this->call(SettingSeeder::class);
        $this->call(\Modules\Tax\database\seeders\TaxDatabaseSeeder::class);
        $this->call(\Modules\Constant\database\seeders\ConstantDatabaseSeeder::class);
        $this->call(\Modules\Commission\database\seeders\CommissionDatabaseSeeder::class);
        $this->call(\Modules\Currency\database\seeders\CurrencyDatabaseSeeder::class);
        $this->call(\Modules\Employee\database\seeders\EmployeeDatabaseSeeder::class);
        $this->call(\Modules\Category\database\seeders\CategoryDatabaseSeeder::class);
        $this->call(\Modules\Service\database\seeders\ServiceDatabaseSeeder::class);
        $this->call(\Modules\Pet\database\seeders\PetDatabaseSeeder::class);
        $this->call(\Modules\Booking\database\seeders\BookingDatabaseSeeder::class);
        $this->call(\Modules\NotificationTemplate\database\seeders\NotificationTemplateSeeder::class);
        $this->call(\Modules\CustomField\database\seeders\CustomFieldDatabaseSeeder::class);
        $this->call(\Modules\Slider\database\seeders\SliderDatabaseSeeder::class);
        $this->call(\Modules\Page\database\seeders\PageDatabaseSeeder::class);
        $this->call(\Modules\Event\database\seeders\EventDatabaseSeeder::class);
        $this->call(\Modules\Blog\database\seeders\BlogDatabaseSeeder::class);
        $this->call(\Modules\Tag\database\seeders\TagDatabaseSeeder::class);
        $this->call(\Modules\World\database\seeders\WorldDatabaseSeeder::class);
        // $this->call(\Modules\Logistic\database\seeders\LogisticZoneTableSeeder::class);
        $this->call(\Modules\Logistic\database\seeders\LogisticDatabaseSeeder::class);
        $this->call(\Modules\Location\database\seeders\LocationDatabaseSeeder::class);
        $this->call(\Modules\Product\database\seeders\ProductDatabaseSeeder::class);
        Schema::enableForeignKeyConstraints();
        // $this->call(BookingsTableSeeder::class);
        // $this->call(BookingTransactionsTableSeeder::class);
        // $this->call(BookingBoardingMappingTableSeeder::class);
        // $this->call(BookingDaycareMappingTableSeeder::class);
        // $this->call(BookingGroomingMappingTableSeeder::class);
        // $this->call(BookingTrainingMappingTableSeeder::class);
        // $this->call(BookingVeterinaryMappingTableSeeder::class);
        // $this->call(BookingWalkingMappingTableSeeder::class);
        // $this->call(CommissionEarningsTableSeeder::class);
        // $this->call(EmployeeRatingTableSeeder::class);
        // $this->call(ProductsTableSeeder::class);
        $this->call(TagsTableSeeder::class);
        $this->call(HerramientaEntrenamientoTypeSeeder::class);
        // $this->call(ProductTagsTableSeeder::class);
        // $this->call(ProductVariationsTableSeeder::class);
        // $this->call(ProductVariationCombinationsTableSeeder::class);
        // $this->call(ProductVariationStocksTableSeeder::class);

        //cargar wallet
        $this->call(WalletSeeder::class);
        //cargar evento
        $this->call(EventSeeder::class);
        //categorycomandos
        $this->call(CategoryComandosSeeder::class);
        $this->call(ComandosSeeder::class);
        //herramientas
        $this->call(TrainingToolsSeeder::class);
        //Plataforma
        $this->call(PlatformSeeder::class);
        //cursos
        $this->call(CoursePlatformSeeder::class);
        //blog
        //$this->call(BlogSeeder::class);
        //Ebooks
        $this->call(EBookSeeder::class);
        //slug in user
        $this->call(InsertSlugInUserSeeder::class);
        //permission profile
        $this->call(PermissionProfilesSeeder::class);
        //expiration
        $this->call(ExpiryDateSeeder::class);
        //seeder specialityRole
        $this->call(SpecialityRoleSeeder::class);
        //Notificacion por usuario nuevo
        $this->call(NotificationUserRegisterTemplateSeeder::class);
        //categorias del diario
        $this->call(DiaryCategorySeeder::class);
    }
}
