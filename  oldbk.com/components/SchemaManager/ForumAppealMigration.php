<?php


namespace components\SchemaManager;

use components\Component\Slim\Slim;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class ForumAppealMigration
 * @package components\SchemaManager
 */
class ForumAppealMigration extends Migration
{

    /**
     *
     */
    public function up()
    {
        $app = Slim::getInstance();
        $token = $app->request->get('_token');

        if ($token == '3d3631cae772d2998cb54d5d247baaee') {
            Schema::dropIfExists('forum_appeal');

            Schema::create('forum_appeal', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('top_id')->index();
                $table->integer('post_id');
                $table->integer('author_id')->nullable()->default(null);
                $table->integer('user_id')->nullable()->default(null);
                $table->integer('moderator_id')->nullable()->default(null);
                $table->timestamps();
                $table->softDeletes();
            });
        }

    }

    /**
     *
     */
    public function down()
    {
        Schema::dropIfExists('forum_appeal');
    }
}