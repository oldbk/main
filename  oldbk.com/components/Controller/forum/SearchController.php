<?php


namespace components\Controller\forum;


use components\Eloquent\Forum;
use components\Eloquent\User;
use components\Eloquent\UsersNickHist;
use components\Exceptions\ForumException;
use components\Helper\Str;

class SearchController extends ForumController
{
    const ENCODING = 'windows-1251';

    /**
     * @param $id
     *
     * Поиск
     */
    public function searchAction($id)
    {

        try {

            if (!$this->user) {
                return $this->app->redirectTo('forum');
            }

            $request = $this->app->request;

            $categories = $this->getCategories(true);

            $this->app->view()->appendLayoutData(array(
                'page_title' => 'Поиск | ' . $this->title,
                'page_description' => $this->description,
                'categories' => $categories,
            ));

            $posts = false;
            $word = $request->get('word');

            if (!is_null($word)) {

                $word = \Xss::clean(trim($word));

                if ($word == '') {
                    throw new ForumException('Попробуйте ввести слово для поиска');
                }

                if (Str::length($word, static::ENCODING) < 3) {
                    throw new ForumException('Минимальное количество символов для поиска 3 и более');
                }

                $allowed_conf = $categories->pluck('id');

                $search_by = $request->params('search_by');
                $strict = $request->params('strict');
                $collection_result = [];

                //поиск по логину
                if ($search_by === 'login') {

                    $author = User::where('login', $word)->first();

                    if ($author) {

                        $ids = $author->posts()->limit(1000)->orderByDesc('id')->pluck('id');

                    } else {
                        $old_login = UsersNickHist::where('old_login', '=', $word)->first(['uid']);

                        if (!$old_login) {
                            throw new ForumException('Такого персонажа даже не существовало...');
                        }

                        $ids = Forum::where('author', '=', $old_login->uid)->limit(1000)->orderByDesc('id')->pluck('id');
                    }


                } else {//по фразе

                    try {
                        $results = Forum::elasticSearch($word, $strict);
                    } catch (\Exception $exc) {
                        throw new ForumException('Поиск результатов не дал, попробуйте другой запрос...' . $exc->getMessage());
                    }

                    $ids = collect();
                    foreach ($results as $item) {
                        $ids->push($item->getId());
                        $collection_result[$item->getId()] = $item->getHighlights();
                    }

                    $results = null;
                }

                if ($ids->isEmpty()) {
                    throw new ForumException('Поиск результатов не дал, попробуйте другой запрос...');
                }


                $posts_query = Forum::select([
                    'f1.*',
                    'f2.topic as mtop',
                ])
                    ->from('forum as f1')
                    ->join('forum as f2', 'f2.id', '=', 'f1.parent')
                    ->leftJoin('forum as f3', 'f3.id', '=', 'f2.parent');

                $posts_query->whereIn('f1.id', $ids);

                $posts_query->where(function ($q) use ($allowed_conf) {
                    $q->whereIn('f2.id', $allowed_conf);
                    $q->orWhereIn('f3.id', $allowed_conf);
                });

                $conf_id = intval($request->get('conf_id', 0));

                if ($conf_id > 100) {
                    throw new ForumException('Не надо так делать =(');
                }

                //поиск в выбранной конфе
                if ($conf_id > 0 && $conf_id < 100) {

                    if (!$categories->find($conf_id)) {
                        throw new ForumException('Не надо так делать =(');
                    }

                    if (in_array($conf_id, $allowed_conf->toArray())) {
                        $posts_query->where(function ($q) use ($conf_id) {

                            $q->where(function ($q) use ($conf_id) {
                                $q->where('f2.id', $conf_id);
                            });

                            $q->orWhere(function ($q) use ($conf_id) {
                                $q->where('f3.id', $conf_id);
                            });

                        });
                    }

                }

                if (!$this->user->isAdmin()) {

                    $posts_query->where(function ($q) {

                        $q->where(function ($q) {
                            $q->where('f1.only_own', 1);
                            $q->where('f1.author', $this->user->id);
                        });

                        $q->orWhere(function ($q) {
                            $q->where('f2.only_own', 1);
                            $q->where('f2.author', $this->user->id);
                        });

                        $q->orWhere(function ($q) {
                            $q->where('f1.only_own', 0);
                            $q->where('f2.only_own', 0);
                        });

                    });

                    $posts_query->where(function ($q) {

                        $q->where(function ($q) {
                            $q->whereNull('f3.id');
                        });

                        $q->orWhere(function ($q) {
                            $q->where('f3.only_own', 1);
                            $q->where('f3.author', $this->user->id);
                        });

                        $q->orWhere(function ($q) {
                            $q->where('f3.only_own', 0);
                        });

                    });

                    if (!$this->user->isPaladin()) {

                        $posts_query->where(function ($q) {

                            $q->where(function ($q) {
                                $q->whereNull('f3.id');
                                $q->where('f2.is_closed', 0);
                            });

                            $q->orWhere(function ($q) {
                                $q->where('f3.is_closed', 0);
                            });

                        });

                        if ($this->user->level > 7) {
                            $posts_query->where('f1.id', '!=', 48);
                        }

                        $posts_query->where(function ($q) {

                            $q->whereNull('f1.delpal');
                            $q->WhereNull('f2.deltoppal');

                        });

                    }

                }

                $posts_query->orderByDesc('id');

                $posts = $posts_query->paginate(20, ['*'], 'page', $request->get('page'));

                if ($posts->total() === 0) {
                    throw new ForumException('Поиск результатов не дал, попробуйте другой запрос...');
                }


                $posts->getCollection()->map(function (Forum $topic, $key) use ($word, $posts, $search_by, $strict, $collection_result) {

                    $tmp_info = $topic->a_info;
                    $topic['post_author'] = [];
                    if ($tmp_info[4] > 0) {

                        if (!$this->user->isAdmin()) {
                            $posts->forget($key);
                            return false;
                        }

                        $topic['post_author'] = [
                            'id' => $topic->author,
                            'klan' => $tmp_info[1],
                            'align' => $tmp_info[2],
                            'level' => $tmp_info[3],
                            'login' => $tmp_info[0] . '(<i>Невидимка</i>)',
                        ];

                    } else {

                        $topic['post_author'] = [
                            'id' => $topic->author,
                            'klan' => $tmp_info[1],
                            'align' => $tmp_info[2],
                            'level' => $tmp_info[3],
                            'login' => $tmp_info[0],
                        ];

                    }

                    if ($search_by == 'text') {
                        $topic['text'] =
                            stripos($topic['text'], "href") === false &&
                            isset($collection_result[$topic->id]) &&
                            isset($collection_result[$topic->id]['text'])
                                ? Str::closetags(iconv('utf-8', 'windows-1251', join('', $collection_result[$topic->id]['text'])))
                                : Str::closetags($topic['text']);
                    }

                    return $topic;
                });


                $posts->appends($request->get());
            }

            $this->render('forum/search/search', [
                'word' => $word,
                'topics' => $posts,
                'categories' => $categories,
                'elements' => $posts ? $this->makeElements($posts) : false,
            ]);

        } catch (ForumException $exception) {
            $this->app->flash('noty', [
                'type' => 'error',
                'msg' => $exception->getMessage(),
            ]);

            //Сохраняем поля для input
            $this->app->flash('word', $request->get('word'));

            $this->app->redirectTo('forum_search');
        }

    }

}