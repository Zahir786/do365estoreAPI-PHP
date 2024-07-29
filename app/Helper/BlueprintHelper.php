<?php

namespace App\Helper;

use Illuminate\Database\Schema\Blueprint;

class BlueprintHelper
{

    static public function BaseEntity(Blueprint $blueprint)
    {
        $blueprint->bigIncrements("id")->index();
        $blueprint->boolean("status")->default(1);
        $blueprint->unsignedBigInteger("created_by_id");
        $blueprint->dateTime("created_on")->useCurrent();
        $blueprint->unsignedBigInteger("updated_by_id")->nullable();
        $blueprint->dateTime("updated_on")->nullable();
        $blueprint->unsignedBigInteger("company_id")->nullable();
    }
}
