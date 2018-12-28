<?php
namespace components\Helper;


use components\Eloquent\User;
use components\Enum\UserAlign;

/**
 * Class AuthorInfo
 * @package components\Helper
 */
class AuthorInfo
{
    /**
     * @param $comment
     * @param $user
     * @return mixed
     */
    public static function buildForComment($comment, $user)
    {

        if ($comment['author']['invisible'] !== 0) {

            $login = null;
            $level = null;
            $canSeeWhoModerator = false;

            if ($comment['author']['invisible'] > 0 || $comment['author']['invisible'] === -1) {
                $login = '<i>Невидимка</i>';
                $level = '??';
            }
            elseif ($comment['author']['invisible'] === -2) {

                if (
                    UserAlign::isValueAdmin($comment['author']['align']) ||
                    UserAlign::isValueAdminClan($comment['author']['klan'])
                ) {
                    $login = '<i>Ангел</i>';
                } else {
                    $login = '<i>Паладин</i>';
                }

            }


            if (
                ($user && ($user->isAdmin() || $user->isAdminion())) ||
                ($user && $user->canSeeWhoModerator() && (!UserAlign::isValueAdmin($comment['author']['align']) || !UserAlign::isValueAdminClan($comment['author']['klan']))) ||
                ($user && $user->id == $comment['author']['id'])
            ) {
                $canSeeWhoModerator = true;
            }

            $comment['author'] = [
                'login' => $login,
                'klan' => '',
                'align' => 0,
                'level' => $level,
                'invisible' => $comment['author']['invisible'],
                'id' => $comment['author']['id'],
                'canViewInfo' => $canSeeWhoModerator,
            ];
        }

        return $comment;

    }

    /**
     * @param $moderator_info
     * @param $user
     * @return mixed
     */
    public static function buildForModerator($moderator_info, $user)
    {

        $canSeeWhoModerator = false;

        if (
            ($user && ($user->isAdmin() || $user->isAdminion())) ||
            ($user && $user->canSeeWhoModerator() && (!UserAlign::isValueAdmin($moderator_info['align']) || !UserAlign::isValueAdminClan($moderator_info['klan']))) ||
            ($user && $user->id == $moderator_info['id'])
        ) {
            $canSeeWhoModerator = true;
        }

        $who = '';
        if ($moderator_info['invisible'] > 0) {
            $who = '<i>Невидимкой</i>';
        }
        elseif (UserAlign::isValuePaladinRang($moderator_info['align'])) {
            $who = '<i>Паладином</i>';
        }
        elseif (
            UserAlign::isValueAdmin($moderator_info['align']) ||
            UserAlign::isValueAdminClan($moderator_info['klan'])
        ) {
            $who = '<i>Ангелом</i>';
        }

        $moderator_info['canViewInfo'] = $canSeeWhoModerator;
        $moderator_info['who'] = $who;

        return $moderator_info;

    }

    /**
     * @param $author_id
     * @param $author_info
     * @param $user
     * @return array
     */
    public static function buildForPostAuthor($author_id, $author_info, $user)
    {
        $invisible_info = false;

        if ($author_info[4] > 0) {

            if(
                ($user && ($user->isAdmin() || $user->isAdminion())) ||
                ($user && $user->canSeeInvisibleAuthor() && (!UserAlign::isValueAdmin($author_info[2]) || !UserAlign::isValueAdminClan($author_info[1])))
            ) {
                $invisible_info = [
                    'id' => $author_id,
                    'klan' => $author_info[1],
                    'align' => $author_info[2],
                    'level' => $author_info[3],
                    'login' => $author_info[0],
                ];
            }

            return [
                'id' => $author_info[4],
                'klan' => '',
                'align' => 0,
                'level' => '??',
                'login' => '<i>Невидимка</i>',
                'is_invisible' => true,
                'invisible_info' => $invisible_info,
            ];

        }

        return [
            'id' => $author_id,
            'klan' => $author_info[1],
            'align' => $author_info[2],
            'level' => $author_info[3],
            'login' => $author_info[0],
            'is_invisible' => false,
            'invisible_info' => $invisible_info,
        ];

    }
}