<?php


namespace components\SchemaManager;

use components\Component\Slim\Slim;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class ForumPermissionMigration
 * @package components\SchemaManager
 */
class ForumModeratorMigration extends Migration
{

    /**
     *
     */
    public function up()
    {
        $app = Slim::getInstance();
        $token = $app->request->get('_token');

        if ($token == '3d3631cae772d2998cb54d5d247baaee') {
            Schema::dropIfExists('forum_moderator');

            Schema::create('forum_moderator', function (Blueprint $table) {
                $table->integer('user_id')->primary();
                $table->text('permissions')->nullable()->default(null);
            });
        }

    }

    /**
     *
     */
    public function down()
    {
        Schema::dropIfExists('forum_moderator');
    }
}