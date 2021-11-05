<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class Computerun2AdditionalFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $fields = DB::table('fields');
        $fields->insert(['id' => 'promo.business_it_case', 'name' => 'Promo Code for Business-IT Case Competition', 'editable' => true]);
        $fields->insert(['id' => 'promo.business_it_case_bundle', 'name' => 'Promo Code for Business-IT Case Competition (Bundle)', 'editable' => true]);
        $fields->insert(['id' => 'promo.sprint', 'name' => 'Promo Code for SPRINT', 'editable' => true]);
        $fields->insert(['id' => 'promo.web_design', 'name' => 'Promo Code for Web Design Competition', 'editable' => true]);
        $fields->insert(['id' => 'promo.web_design_bundle', 'name' => 'Promo Code for Web Design Competition (Bundle)', 'editable' => true]);
        $fields->insert(['id' => 'promo.workshop', 'name' => 'Promo Code for Workshop', 'editable' => true]);
        $fields->insert(['id' => 'location.country', 'name' => 'Country of Origin', 'editable' => true]);
        $fields->insert(['id' => 'location.home_address', 'name' => 'Home Address', 'editable' => true]);
        $fields->insert(['id' => 'location.indonesia.province', 'name' => 'Domicile Province', 'editable' => true]);
        $fields->insert(['id' => 'location.postal_code', 'name' => 'Postal Code', 'editable' => true]);
        $fields->insert(['id' => 'merchandise.tshirt.size', 'name' => 'T-Shirt Merchandise Size', 'editable' => true]);
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $user_properties = DB::table('user_properties');
        $user_properties->where('field_id', 'promo.business_it_case')
            ->orWhere('field_id', 'promo.business_it_case_bundle')
            ->orWhere('field_id', 'promo.sprint')
            ->orWhere('field_id', 'promo.web_design')
            ->orWhere('field_id', 'promo.web_design_bundle')
            ->orWhere('field_id', 'promo.workshop')
            ->orWhere('field_id', 'location.country')
            ->orWhere('field_id', 'location.home_address')
            ->orWhere('field_id', 'location.indonesia.province')
            ->orWhere('field_id', 'location.postal_code')
            ->orWhere('field_id', 'merchandise.tshirt.size')
            ->delete();
    }
}
