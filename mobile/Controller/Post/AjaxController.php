<?php
/**
 * Created by PhpStorm.
 * User: ThanhDT
 * Date: 12/4/2018
 * Time: 9:19 AM
 */

namespace Mobile\Controller\Post;

use Mobile\Controller\BaseController;
use App\Utils\Constants;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AjaxController extends BaseController
{
    /**
     * author: TrieuNT
     * create date: 2018-12-11 09:31 AM
     */
    const code_base64 = "R0lGODlhAQABAIAAAP///wAAACwAAAAAAQABAAACAkQBADs=";


    /**
     * @author: TrieuNT
     * @return bool
     */
    public function is_bot_detected()
    {
        if (isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/bot|crawl|slurp|spider/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
            return true;
        }
        return false;
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \Exception
     */
    public function updateView(Request $request)
    {
        if (!($this->is_bot_detected())) {
            $type = (int)$request->get('type');
            $postId = (int)$request->get('id');
            if ($type && $postId) {
                switch ($type) {
                    case 5:
                        // update share fb
                        $keyQueue = Constants::FACEBOOK_VIEW_KEY_QUEUE;
                        break;
                    case 6:
                        // update fb like
                        $keyQueue = Constants::FACEBOOK_LIKE_KEY_QUEUE;
                        break;
                    case 7:
                        // update fb G+
                        $keyQueue = Constants::GOOGLE_LIKE_KEY_QUEUE;
                        break;
                    default:
                        // update view article
                        $keyQueue = Constants::ARTICLE_VIEW_QUEUE;
                        break;
                }

                $redis = $this->getCacheProvider(Constants::SERVER_CACHE_UPDATE_VIEW);
                $redis->lpush($keyQueue, $postId . '|' . time());
            }
        }
        $code_binary = base64_decode(self::code_base64);
        $image = imagecreatefromstring($code_binary);
        imagegif($image);
        imagedestroy($image);
        $response = new Response();
        $response->headers->set('Content-Type', 'image/gif');
        return $response;
    }
}
