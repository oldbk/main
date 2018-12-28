<?php

namespace components\IndexManager;

use components\Component\Cli\CommandLine;
use components\Component\Elastica\Forum\Indexer;
use components\Eloquent\Forum;

/**
 * Class ForumIndex
 * @package components\IndexManager
 */
class ForumIndex
{

    /**
     * @return bool|\Elastica\Response
     */
    public function up($params)
    {
        $model = new Forum();

        $ifArgs = count($params) > 0;

        $indexer = new Indexer($model->getIndexName(), $model->getTypeName(), !$ifArgs);
        $indexer->initSettings(100);

		\DB::statement('SET NAMES utf8');
		$posts = $model->select(['id', 'text', 'updated'])->where('type', '>', 1);

        if ($ifArgs) {

            foreach ($params as $key => $param) {

                switch ($key) {

                    case 'from':
                        {

                            $posts->whereDate('updated', '>=', $param);

                            break;
                        }

                    case 'to':
                        {

                            $posts->whereDate('updated', '<=', $param);

                            break;
                        }

                }

            }

        }

        $posts->orderBy('id', 'desc');

        $posts->chunk(1000, function ($posts) use ($indexer) {
            $indexer->run($posts->toArray());
            echo "Next iteration \n";
        });

        if (!$ifArgs) {
            $indexer->refresh();
        }

        return "Done!";
    }

    /**
     *
     */
    public function down()
    {

    }
}