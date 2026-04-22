<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('vendors', function (Blueprint $table) {
        $table->string('business_type');
        $table->string('contact_person_name');
        $table->string('gst_number')->nullable();
        $table->string('city');
        $table->string('state');
        $table->string('pincode', 6);
        $table->string('account_holder_name');
        $table->text('account_number_encrypted');
        $table->string('ifsc_code', 11);
    });
}

    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropColumn([
                'business_type',
                'contact_person_name',
                'gst_number',
                'city',
                'state',
                'pincode',
                'account_holder_name',
                'account_number_encrypted', // updated name
                'ifsc_code',
            ]);
        });
    }
};