<?php
/**
 * Created by PhpStorm.
 * User: Do Tien Thanh
 * Date: 6/17/2017
 * Time: 10:09 AM
 */

namespace App\Service;

use App\Utils\Constants;
use App\Utils\Lib;
use App\Utils\WordPressLib;
use Symfony\Component\Routing\Router;
use Symfony\Component\Validator\Constraints\Date;

class DataExchange
{
    const DATA_EXCHANGE = 'DataExchange';
    private $imageService;
    private $category;
    private $domain;
    private $router;

    public function __construct(Router $router, string $domain, Images $imageService, Category $category)
    {
        $this->router = $router;
        $this->imageService = $imageService;
        $this->domain = $domain;
        $this->category = $category;
    }

    public function exchangeArrayArticle($data, $imageSize = Constants::POST_AVATAR_LIST_SIZE, $specialImageSize = null, $cutNumberCharactersTile = Constants::CUT_NUMBER_CHARACTERS_DEFAULT,$cutNumberCharactersSapo = Constants::CUT_NUMBER_CHARACTERS_DEFAULT)
    {
        $newData = [];
        if ($data) {
            foreach ($data as $index => $item) {
                if ($index == 0 && $specialImageSize) {
                    $newData[] = $this->exchangeRowPost($item, $specialImageSize,$cutNumberCharactersTile,$cutNumberCharactersSapo);
                } else {
                    $newData[] = $this->exchangeRowPost($item, $imageSize,$cutNumberCharactersTile,$cutNumberCharactersSapo);
                }
            }
        }

        return $newData;
    }

    public function exchangeArrayArticleStream($data, $imageSize = Constants::POST_AVATAR_LIST_SIZE, $specialImageSize = null,$cutNumberCharactersTile = Constants::CUT_NUMBER_CHARACTERS_DEFAULT,$cutNumberCharactersSapo = Constants::CUT_NUMBER_CHARACTERS_DEFAULT)
    {
        $newData = [];
        if ($data) {
            foreach ($data as $index => $item) {
                $articleId = $item['postId'];
                $publishedDate = isset($item['publishedDate']) ? $item['publishedDate'] : new \DateTime(Constants::MIN_DATE);
                $newItem = [];
                $newItem['id'] = $articleId;
                $newItem['slug'] = $item['slug'];
                $newItem['s_publish_time'] = Lib::formatDate($publishedDate);
                $newItem['publishedTimestamp'] = $item['publishedTimestamp'];
                $newItem['publish_time_ago'] = Lib::fomartMetaDate($publishedDate);
                $newItem['title'] = $cutNumberCharactersTile? Lib::subWords($item['title'], $cutNumberCharactersTile):$item['title'];
                // mobile
                if($index == 0){
                    $newItem['title_mobile'] = $cutNumberCharactersTile? Lib::subWords($item['title'], $cutNumberCharactersTile):$item['title'];
                } else {
                    $newItem['title_mobile'] = Lib::subString($item['title'], Constants::CUT_NUMBER_CHARACTERS_FEATURED_TOP_TITLE_V2);
                }
                $newItem['titleHot'] = $item['title'];
                if (isset($item['postType'])) {
                    $newItem['media_html'] = $this->buildMediaHtml($item['postType']);
                }
                if (isset($item['sapo'])) {
//                    $newItem['sapo'] = Lib::subString($item['sapo'], 200);
                    $newItem['sapo'] = $cutNumberCharactersSapo? Lib::subWords($item['sapo'], $cutNumberCharactersSapo):$item['sapo'];
                }
                if ($index == 0 && $specialImageSize) {
                    $newItem['avatar'] = $this->imageService->getImageSize($item['avatar'], $specialImageSize);
                } else {
                    $newItem['avatar'] = $this->imageService->getImageSize($item['avatar'], $imageSize);
                }
                if (isset($item['cateSlug'])) {
                    $newItem['cate_url'] = $this->router->generate('cate', ['cateSlug' => $item['cateSlug']]);
                    $newItem['cate_name'] = $item['cateName'];
                    $cateSlug = $item['cateSlug'];
                } else {
                    $newItem['cate_url'] = '#';
                    $newItem['cate_name'] = 'Unknown';
                    $cateInfo = $this->category->getCateById($item['cateId']);
                    $cateSlug = $cateInfo ? $cateInfo['slug'] : 'cate';
                }
                $newItem['url'] = $this->router->generate('post_detail', ['cateSlug' => $cateSlug, 'slug' => $item['slug'], 'postId' => $item['postId']]);
                $newData[] = $newItem;
            }
        }

        return $newData;
    }

    /**
     * Exchange data for home stream posts
     * author: ThanhDT
     * date:   2018-12-24 09:51 AM
     * @param $data
     * @param $featuredData
     * @param $positionInject
     * @param string $imageSize
     * @param null $specialImageSize
     * @return array
     */
    public function exchangeFeaturedArrayArticle($data, $featuredData, $positionInject, $imageSize = Constants::POST_AVATAR_LIST_SIZE, $specialImageSize = null)
    {
        $newData = [];
        if ($data) {
            $isInjected = false;
            foreach ($data as $index => $item) {
                $newData[] = $this->exchangeRowPost($item, $imageSize);
                if ($featuredData && ($index + 1 == $positionInject)) {
                    $newData[] = $this->exchangeRowPost($featuredData, $specialImageSize);
                    $isInjected = true;
                }
            }
            if (!$isInjected) {
                $newData[] = $this->exchangeRowPost($featuredData, $specialImageSize);
            }
        }

        return $newData;
    }

    /**
     * Exchange Row Post
     * author: ThanhDT
     * date:   2018-12-24 09:45 AM
     * @param $item
     * @param $imageSize
     * @return array
     */
    private function exchangeRowPost($item, $imageSize,$cutNumberCharacters,$cutNumberCharactersSapo)
    {
        $articleId = $item['postId'];
        $publishedDate = isset($item['publishedDate']) ? $item['publishedDate'] : new \DateTime(Constants::MIN_DATE);
        $newItem = [];
        $newItem['id'] = $articleId;
        $newItem['slug'] = $item['slug'];
        $newItem['s_publish_time'] = Lib::formatDate($publishedDate);
        $newItem['publishedTimestamp'] = $item['publishedTimestamp'];
        $newItem['publish_time_ago'] = Lib::fomartMetaDate($publishedDate);
        $newItem['title'] = $cutNumberCharacters? Lib::subWords($item['title'], $cutNumberCharacters):$item['title'];
        $newItem['titleHot'] = $item['title'];
        if (isset($item['postType'])) {
            $newItem['media_html'] = $this->buildMediaHtml($item['postType']);
            /*if($articleId == 867) {var_dump($item);die;
                var_dump($item['postType']);
                var_dump($newItem['media_html']);die;
            }*/
        }
        if (isset($item['sapo'])) {
            $newItem['sapo'] = $cutNumberCharactersSapo? Lib::subWords($item['sapo'], $cutNumberCharactersSapo):$item['sapo'];
        }
        $newItem['avatar'] = $this->imageService->getImageSize($item['avatar'], $imageSize);
        if (isset($item['cateSlug'])) {
            $newItem['cate_url'] = $this->router->generate('cate', ['cateSlug' => $item['cateSlug']]);
            $newItem['cate_name'] = $item['cateName'];
            $cateSlug = $item['cateSlug'];
        } else {
            $newItem['cate_url'] = '#';
            $newItem['cate_name'] = 'Unknown';
            $cateInfo = $this->category->getCateById($item['cateId']);
            $cateSlug = $cateInfo ? $cateInfo['slug'] : 'cate';
        }
        $newItem['url'] = $this->router->generate('post_detail', ['cateSlug' => $cateSlug,'slug' => $item['slug'], 'postId' => $item['postId']]);
        return $newItem;
    }

    public function exchangeReviewPosts($posts, $imageSize = Constants::POST_AVATAR_LIST_SIZE)
    {
        $newData = [];
        if ($posts) {
            foreach ($posts as $index => $item) {
                $articleId = $item['postId'];
                $newItem = [];
                $newItem['id'] = $articleId;
                $newItem['slug'] = $item['slug'];
                $newItem['title'] = $item['title'];
                $newItem['avatar'] = $this->imageService->getImageSize($item['avatar'], $imageSize);
                $newItem['review_points'] = Lib::formatPoint($item['reviewPoints']);
                $newItem['other_tags'] = $item['otherTags']?$item['otherTags']:'Unknown';
                $cateInfo = $this->category->getCateById($item['cateId']);
                $cateSlug = $cateInfo ? $cateInfo['slug'] : 'cate';
                $newItem['url'] = $this->router->generate('post_detail', ['cateSlug' => $cateSlug, 'slug' => $item['slug'], 'postId' => $item['postId']]);
                $newData[] = $newItem;
            }
        }

        return $newData;
    }

    /**
     * Content: Exchange article detail
     * author: TrieuNT
     * create date: 2018-10-23 10:11 AM
     * @param $data
     * @param string $imageSize
     * @return array
     */
    public function exchangeArticleDetail($data, $imageSize = Constants::POST_AVATAR_LIST_SIZE)
    {
        $newData = [];
        //$tagHtml = '';
        if ($data) {
            $imageService = $this->imageService;
            $articleId = $data['postId'];
            $publishedDate = isset($data['publishedDate']) ? $data['publishedDate'] : new \DateTime(Constants::MIN_DATE);
            $newData['id'] = $articleId;
            $newData['slug'] = $data['slug'];
            $newData['authorId'] = $data['authorId'];
            $newData['authorName'] = $data['authorName'];
            //$newData['reviewId'] = $data['reviewId'];
            $newData['cateId'] = $data['cateId'];
            $newData['cate_url'] = $this->router->generate('cate', ['cateSlug' => $data['cateSlug']]);
            $newData['cate_name'] = $data['cateName'];
            $newData['cateSlug'] = $data['cateSlug'];
            /*$newData['catePrimary'] = !empty($data['cateName']) ? '<a href="' . $this->router->generate('cate', ['cateSlug' => $data['cateSlug']]) . '" class="lnk-org">' . $data['cateName'] . '</a>' : '---';*/
            $newData['s_publish_time'] = Lib::formatDate($publishedDate);
            $newData['publish_time_ago'] = Lib::fomartMetaDate($publishedDate);
            //$newData['meta_publish_time'] = Lib::fomartMetaDate($publishedDate);
            $newData['title'] = $data['title'];
            $newData['sapo'] = $data['sapo'];
            $newData['content'] = $data['content'];
            $newData['contentMobile'] = $data['contentMobile'];
            $newData['contentAmp'] = $data['contentAmp'];
            $newData['relatedPosts'] = $data['relatedPosts'];
            if ($data['tags']) {
                $newData['tags'] = json_decode($data['tags'], true);
//                foreach ($data['tags'] as $tagId => $item) {
//                    $tagHtml .= ($tagHtml == '') ? '<li class="item"><a href="' . $this->router->generate('tag', ['tagSlug' => $item['s']]) . '" title="' . $item['n'] . '" rel="tag">#' . $item['n'] . '</a></li>' : ' ' . '<li class="item"><a href="' . $this->router->generate('tag', ['tagSlug' => $item['s']]) . '" title="' . $item['n'] . '" rel="tag">#' . $item['n'] . '</a></li>';
//                }
            } else {
                $newData['tags'] = null;
            }
//            $newData['tags'] = $tagHtml;
            $newData['avatar'] = $imageService->getImageSize($data['avatar'], $imageSize);
            $newData['url'] = $this->router->generate('post_detail', ['cateSlug' => $data['cateSlug'], 'slug' => $data['slug'], 'postId' => $articleId]);
            $newData['shortLink'] = $this->router->generate('post_detail_short', ['postId' => $articleId]);
            // Add SEO info
            $seo = array(
                'url' => $newData['url'],
                'title' => empty($data['seoTitle']) ? $data['title'] : $data['seoTitle'],
                'description' => empty($data['seoMetadesc']) ? $data['sapo'] : $data['seoMetadesc'],
                'image' => $imageService->getImageSize($data['avatar'], Constants::SIZE_IMG_LINK_SHARE_POST_DETAIL),
                'publish_time' => Lib::fomartMetaDate($publishedDate),
                'og_type' => 'article',
                'amp'=> true,
            );
            $newData['seo'] = $seo;
        }

        return $newData;
    }

    /**
     * Exchange article related
     * @param $data
     * @param $option
     * @return array
     * author : ThangPD
     * date : 2018-34-22 10:34 AM
     */
    public function exchangeRelatedArticles($data, $option)
    {
        $newData = [];
        if ($data) {
            $imageService = $this->imageService;
            //$domain = $this->domain;
            foreach ($data as $item) {
                $articleId = $item['postId'];
                $publishedDate = isset($item['publishedDate']) ? $item['publishedDate'] : new \DateTime(Constants::MIN_DATE);
                $newItem = [];
                $newItem['id'] = $articleId;
                $newItem['slug'] = $item['slug'];
                $newItem['s_publish_time'] = Lib::formatDate($publishedDate);
                $newItem['publish_time_ago'] = Lib::fomartMetaDate($publishedDate);
                //$item['s_day_time'] = isset($item['publishtime']) ? Lib::sw_get_current_weekday($item['publishtime']) : '';
                $newItem['title'] = $option == 0 ? $item['title'] : Lib::subString($item['title'], 110);
                if ($option == 2) {
                    $imageSize = Constants::POST_AVATAR_RELATED_ARTICLES_SIZE;
                } else {
                    $imageSize = Constants::POST_AVATAR_FEATURED_STORIES;
                }
                $newItem['avatar'] = $imageService->getImageSize($item['avatar'], $imageSize);
                $newItem['url'] = $this->router->generate('post_detail', ['cateSlug' => $item['cateSlug'], 'slug' => $newItem['slug'], 'postId' => $articleId]);
                $newItem['cate_url'] = $this->router->generate('cate', ['cateSlug' => $item['cateSlug']]);
                $newItem['cate_name'] = $item['cateName'];
                $newItem['sapo'] = $item['sapo'];
                $newData[] = $newItem;
            }
        }

        return $newData;
    }

    public function exchangeRelatedArticlesMobile($data, $option){
        $newData = [];
        if ($data) {
            $imageService = $this->imageService;
            //$domain = $this->domain;
            foreach ($data as $item) {
                $articleId = $item['postId'];
                $publishedDate = isset($item['publishedDate']) ? $item['publishedDate'] : new \DateTime(Constants::MIN_DATE);
                $newItem = [];
                $newItem['id'] = $articleId;
                $newItem['slug'] = $item['slug'];
                $newItem['s_publish_time'] = Lib::formatDate($publishedDate);
                $newItem['publish_time_ago'] = Lib::fomartMetaDate($publishedDate);
                //$item['s_day_time'] = isset($item['publishtime']) ? Lib::sw_get_current_weekday($item['publishtime']) : '';
                $newItem['title'] = $option == 0 ? $item['title'] : Lib::subString($item['title'], 140);
                if ($option == 2) {
                    $imageSize = Constants::POST_AVATAR_RELATED_ARTICLES_MOBILE_SIZE;
                } else {
                    $imageSize = Constants::POST_AVATAR_FEATURED_STORIES;
                }
                $newItem['avatar'] = $imageService->getImageSize($item['avatar'], $imageSize);
                $newItem['url'] = $this->router->generate('post_detail', ['cateSlug' => $item['cateSlug'], 'slug' => $newItem['slug'], 'postId' => $articleId]);
                $newItem['cate_url'] = $this->router->generate('cate', ['cateSlug' => $item['cateSlug']]);
                $newItem['cate_name'] = $item['cateName'];
                $newItem['sapo'] = Lib::subString($item['sapo'], 200);
                $newData[] = $newItem;
            }
        }

        return $newData;
    }

    /**
     * Exchange tags data
     * author: ThanhDT
     * date:   2018-12-24 10:34 PM
     * @param $tags
     * @param string $imageSize
     * @return array
     */
    public function exchangeTags($tags, $imageSize = Constants::POST_AVATAR_LIST_SIZE)
    {
        $newData = [];
        if ($tags) {
            $imageService = $this->imageService;
            foreach ($tags as $index => $item) {
                $newItem = [];
                $newItem['tag_id'] = $item['tagId'];
                $newItem['name'] =  $item['name'];
                $newItem['url'] = $this->router->generate('tag', ['tagSlug' => $item['slug']]);
                if (isset($item['avatar'])) {
                    $newItem['avatar'] = $imageService->getImageSize($item['avatar'], $imageSize);
                }
                if (isset($item['postCount'])) {
                    $newItem['post_count'] = $item['postCount'];
                }
                if (isset($item['points'])) {
                    $newItem['points'] = Lib::formatPoint($item['points']);
                }
                if (isset($item['otherTags'])) {
                    $newItem['other_tags'] = $item['otherTags'];
                } else {
                    $newItem['other_tags'] = '';
                }
                $newData[] = $newItem;
            }
        }

        return $newData;
    }

    /**
     * exchange Tags Is Special
     * author: AnhPT4
     * date: 2018-4-26 18:4 PM
     * @param $tags
     * @param string $imageSize
     * @return array
     */
    public function exchangeTagsIsSpecial($tags, $imageSize = Constants::POST_AVATAR_LIST_SIZE)
    {
        $newData = [];
        if ($tags) {
            $imageService = $this->imageService;

            $newData['avatar'] = '';
            if (!empty($tags['avatar'])) {
                $newData['avatar'] = $imageService->getImageSize($tags['avatar'], $imageSize);
            }
            $newData['points'] = Lib::formatPoint($tags['points']);
            $newData['summary'] = !empty($tags['summary'])? strip_tags($tags['summary']):'';
            $newData['release_date'] = !empty($tags['releaseDate'])? $tags['releaseDate']:'';
            $newData['ios_link'] = !empty($tags['iosLink'])? $tags['iosLink']:'';
            $newData['android_link'] = !empty($tags['androidLink'])? $tags['androidLink']:'';
            $newData['other_tags'] = !empty($tags['otherTags'])? $tags['otherTags']:'';
            $newData['description'] = !empty($tags['description'])? strip_tags($tags['description']):'';
            return $newData;
        }
    }

    /**
     * Exchange videos data
     * author: ThanhDT
     * date:   2018-12-25 03:36 PM
     * @param $videos
     * @param string $imageSize
     * @return array
     */
    public function exchangeVideos($videos, $imageSize = Constants::POST_AVATAR_LIST_SIZE)
    {
        $newData = [];
        if ($videos) {
            $imageService = $this->imageService;
            foreach ($videos as $index => $video) {
                $newItem = [];
                $newItem['video_id'] = $video['videoId'];
                $newItem['title'] = Lib::subString($video['title'], 100);
                $newItem['url'] = $this->router->generate('video_detail', ['videoSlug' => $video['slug'], 'videoId' => $video['videoId']]);
                $newItem['avatar'] = $imageService->getImageSize($video['avatar'], $imageSize);
                $newData[] = $newItem;
            }
        }

        return $newData;
    }

    /**
     * Content: Exchange RSS
     * author : ThangPD
     * date : 2019-24-4 13:24 PM
     */
    public function ExchangeArticleRss($data, $imageSize = Constants::POST_AVATAR_LIST_SIZE)
    {
        $new_data = [];
        if ($data) {
            foreach ($data as $item) {
                $dataPost = $this->exchangeRowPost($item, $imageSize, 0, 0);
                $dataPost['displayName'] = $item['authorName'];
                $dataPost['guid'] = $this->domain . '/?p=' . $item['postId'];
                $dataPost['content'] = $item['content'];
                $new_data[] = $dataPost;
            }
        }

        return $new_data;
    }

    /**
     * Build media html tag
     * author: ThanhDT
     * date:   2018-12-28 11:19 AM
     * @param $postType
     * @return string
     */
    private function buildMediaHtml($postType)
    {
        switch ($postType){
            case 2: // Video
                return '<span class="tag-video"><i class="icon-videocam"></i></span>';
            case 1: // Image
                return '<span class="tag-video"><i class="icon-picture"></i></span>';
            default:
                return '';
        }
    }

    /**
     * @param $data
     * @param string $imageSize
     * @param null $categories
     * @param null $titleLength
     * @param null $sapoLength
     * @return array
     * @throws \Exception
     */
    public function exchangeArraySearchPost($data, $imageSize = Constants::POST_AVATAR_LIST_SIZE, $categories = null, $titleLength = null, $sapoLength = null)
    {
        $postsData = [];
        if ($data) {
            foreach ($data as $key => $item) {
                if (empty($item['_source']['post_id'])) {
                    continue;
                }
                $newItem = [];
                $newItem['title'] = $item['_source']['title'];
                $newItem['id'] = $item['_source']['post_id'];
                $newItem['slug'] = $item['_source']['slug'];
                $publishedDate = isset($item['_source']['published_date']) ? new \DateTime($item['_source']['published_date']) : new \DateTime(Constants::MIN_DATE);
                $newItem['s_publish_time'] = Lib::formatDate($publishedDate);
                $newItem['publish_time_ago'] = Lib::fomartMetaDate($publishedDate);
                $newItem['publishedDateTimestamp'] = $publishedDate->getTimestamp();
                $newItem['sapo'] = isset($item['_source']['sapo']) ? $item['_source']['sapo'] : '';

                if ($titleLength && $titleLength > 0) {
                    $newItem['title'] = Lib::subString($newItem['title'], $titleLength);
                }
                if ($sapoLength && $sapoLength > 0) {
                    $newItem['sapo'] = Lib::subString($newItem['sapo'], $sapoLength);
                }

                $newItem['avatar'] = $this->imageService->getImageSize($item['_source']['avatar'], $imageSize);

                if (!empty($item['_source']['cate_id']) && isset($categories[$item['_source']['cate_id']])) {
                    $newItem['cate_name'] =  $categories[$item['_source']['cate_id']]['name'];
                    $cateSlug =  $categories[$item['_source']['cate_id']]['slug'];
                    $newItem['cate_url'] = $this->router->generate('cate', ['cateSlug' => $cateSlug]);
                } else{
                    $newItem['cate_name'] = '';
                    $newItem['cate_url'] = '';
                    $cateSlug = 'other';
                }

                $newItem['url'] = $this->router->generate('post_detail', ['cateSlug' => $cateSlug, 'slug' => $item['_source']['slug'], 'postId' => $item['_source']['post_id']]);
                $postsData[$newItem['id']] = $newItem;
            }
        }
        return $postsData;
    }


    /**
     * @param $item
     * @param $imageSize
     * @param null $lengthTitle
     * @return array
     */
    public function exchangeDataVideoList($item, $imageSize, $lengthTitle = null)
    {
        $imageService = $this->imageService;
        $videoId = $item['videoId'];
        $newItem = [];

        $newItem['id'] = $videoId;
        $newItem['slug'] = $item['slug'];
        if($lengthTitle && $lengthTitle > 0){
            $newItem['title'] = Lib::subString($item['title'], $lengthTitle);
        } else {
            $newItem['title'] = $item['title'];
        }
        $newItem['title_hover'] = $item['title'];
        $newItem['source_video'] = $item['sourceVideo'];
        $newItem['avatar'] = $imageService->getImageSize($item['avatar'], $imageSize);
        $newItem['url'] = ($item['slug']) ? $this->router->generate('video_detail', ['videoSlug' => $item['slug'], 'videoId' => $videoId]) : '';
        return $newItem;
    }

    /**
     * @param $item
     * @param $playListJson
     * @return array
     */
    public function exchangePlayVideoList($item)
    {
        //$playListJson = '';
        //$playListJson .= "{file:'".$item['sourceVideo']."'}"."," ;
        //return $playListJson;
        $playListJson = [];
        $playListJson['file']= $item['sourceVideo'] ;
        return $playListJson;
    }


    /**
     * @param $data
     * @param $imageSize
     * @param null $lengthTitle
     * @return array
     */
    public function exchangeDataVideoArray($data, $imageSize, $lengthTitle = null)
    {
        $videoData = [];
        if($data) {
            foreach ($data as $key => $item) {
                $newItem = [];
                $imageService = $this->imageService;
                $videoId = $item['videoId'];
                $newItem['id'] = $videoId;
                $newItem['slug'] = $item['slug'];
                if ($lengthTitle && $lengthTitle > 0) {
                    $newItem['title'] = Lib::subString($item['title'], $lengthTitle);
                }else {
                    $newItem['title'] = $item['title'];
                }
                $newItem['title_hover'] = $item['title'];
                $newItem['source_video'] = $item['sourceVideo'];
                $newItem['avatar'] = $imageService->getImageSize($item['avatar'], $imageSize);
                $newItem['url'] = ($item['slug']) ? $this->router->generate('video_detail', ['videoSlug' => $item['slug'], 'videoId' => $videoId]) : '';
                $videoData[] = $newItem;
            }
        }
        return $videoData;
    }
}
