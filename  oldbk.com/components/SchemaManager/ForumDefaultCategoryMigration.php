<?php


namespace components\SchemaManager;

use components\Component\Slim\Slim;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class ForumDefaultCategoryMigration
 * @package components\SchemaManager
 */
class ForumDefaultCategoryMigration extends Migration
{

    /**
     *
     */
    public function up()
    {
        $app = Slim::getInstance();
        $token = $app->request->get('_token');

        if ($token == '3d3631cae772d2998cb54d5d247baaee') {
            Schema::dropIfExists('forum_default_category');

            Schema::create('forum_default_category', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('cat_id')->index();
            });
        }

    }

    /**
     *
     */
    public function down()
    {
        Schema::dropIfExists('forum_default_category');
    }
}