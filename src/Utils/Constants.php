<?php
/**
 * Created by PhpStorm.
 * User: ThanhDT
 * Date: 5/17/2018
 * Time: 1:38 PM
 */
namespace App\Utils;

class Constants
{
    // Memcached Cache provider
    /*const SERVER_CACHE_ARTICLE = 'memcachedArticle';
    const SERVER_CACHE_FULL_PAGE = 'memcachedCachePage';*/
    // Redis Cache provider
    const SERVER_CACHE_ARTICLE = 'redisArticle';
    const SERVER_CACHE_FULL_PAGE = 'redisCachePage';
    const SERVER_CACHE_ARTICLE_TYPE = 1; // 0 memcached, 1: redis
    // Sync view using only redis
    const SERVER_CACHE_UPDATE_VIEW = 'redisUpdateView';
    // Exchange min date
    const MIN_DATE = '1970-01-01';
    const MIN_DATE_SITE_MAP = '2018-01-01';
    const MIN_DATE_SITE_MAP_V2 = '2018-10-02';

    // Enum value
    const FOCUS_HOME = 1; // HOME STREAM
    const FOCUS_CATEGORY = 2;
    const FOCUS_FEATURE_STORY = 3;
    const FOCUS_TRENDING_STORY = 4;

    // Category Service
    const CACHE_CATEGORY_ALL_SLUG = '_CACHE:CATEGORY:ALL:SLUG';
    const CACHE_CATEGORY_ALL_SLUG_CHILD = '_CACHE:CATEGORY:ALL:SLUG:CHILD';
    const CACHE_CATEGORY_ALL_CHILD = '_CACHE:CATEGORY:ALL:CHILD';
    const CACHE_CATEGORY_ALL_ID = '_CACHE:CATEGORY:ALL:ID';
    const CACHE_CATEGORY_ALL_TERM_ID = '_CACHE:CATEGORY:ALL:TERM:ID';
    const CACHE_CATEGORY_ALL_PARENT = '_CACHE:CATEGORY:ALL:PARENT';
    const CACHE_CATEGORY_ID = '_CACHE:CATEGORY:ID:%s';

    // Images size
    const IMAGE_SHARE_SIZE = '150x150';
    const POST_AVATAR_LIST_SIZE = '370x220';
    const MOBILE_POST_AVATAR_LIST_SIZE = '218x143';
    const POST_AVATAR_FEATURED_STORIES = '170x110';
    const POST_AVATAR_RELATED_ARTICLES_SIZE = '370x220';
    const HOME_FEATURED_STORY_SIZE = '770x484';
    const FIRST_TOP_FEATURED_SIZE = '770x498';
    const MOBILE_FIRST_TOP_FEATURED_SIZE = '480x254';
    const MOBILE_FIRST_TOP_FEATURED_SIZE_SMALL = '225x233';
    const OTHER_TOP_FEATURED_SIZE = '370x320';
//    const POST_AVATAR_LIST_SIZE = '360x201';
    const IMAGE_GALLERY_LIST_SIZE = '150x150';
    const IMAGE_CATEGORY_FEATURED_STORIES_SIZE = '170x110';
    const IMAGE_FIRST_VIDEO_TOP = '770x498';
    const IMAGE_LIST_VIDEO_TOP = '185x110';

    // Images size mobile
    const POST_AVATAR_RELATED_ARTICLES_MOBILE_SIZE = '218x143';
    
    // Category List
    const CACHE_FOCUS_CATE = 'CACHE:FOCUS:CATE:%s';
    const CACHE_CATEGORY_LASTEST_PAGE = 'CACHE:CATEGORY:LASTEST:PAGE:%s';
    const CACHE_CATEGORY_LASTEST_TIMESTAMP = 'CACHE:CATEGORY:LASTEST:CATE:%s:TIMESTAMP:%s:POST:%s';
    const CACHE_FEATURED_STORY = 'CACHE:FEATURED:STORY';
    
    const TABLE_POST_BY_CATE_PAGE = '_CACHE:POST:CATE:%s:PAGE:%s';
    const TABLE_POST_BY_CATE_COUNT = '_CACHE:POST:CATE:%s:COUNT';
    const TABLE_POST_CATE_HOMEBOX = '_CACHE:POST:CATE:HOMEBOX:%s';
    const TABLE_POST_BY_TAG_PAGE = '_CACHE:POST:TAG:%s:PAGE:%s';
    const TABLE_POST_BY_TAG_COUNT = '_CACHE:POST:TAG:%s:COUNT';
    
    // Tag
    const CACHE_POST_TAG = '_CACHE:POST:TAG:%s';
    const CACHE_POST_TAG_TIMESTAMP = '_CACHE:POST:TAG:%s:TIMESTAMP:%s:POST:%s';
    const TABLE_TAG_ALL_SLUG = '_CACHE:TAG:ALL:SLUG';
    const TABLE_TAG_BY_SLUG = '_CACHE:TAG:SLUG:%s';
    const TABLE_TAG_ALL_ID = '_CACHE:TAG:ALL:ID';

    //Video
    const CACHE_VIDEO_FOCUS_LIST = '_CACHE:VIDEO:FOCUS:LIST';
    const CACHE_VIDEOS_LIST_BOTTOM = '_CACHE:VIDEO:LIST:ALL';
    const CACHE_AJAX_VIDEOS_LIST_BOTTOM = '_CACHE:VIDEO:AJAX:LIST:%s';
    const CACHE_AJAX_VIDEOS_DETAIL_BOTTOM = '_CACHE:VIDEO:AJAX:DETAIL:%s:%s';
    const CACHE_VIDEO_DETAIL_ID = '_CACHE:VIDEO:PAGE:DETAIL:%s';
    const CACHE_VIDEO_FOUR_RIGHT = '_CACHE:VIDEO:FOUR:RIGHT:%s';
    const CACHE_VIDEOS_DETAIL_BOTTOM = '_CACHE:VIDEO:DETAIL:LIST:%s';
    
    // Constant config
    const PAGE_SIZE = 10;
    const MOBILE_PAGE_SIZE = 10;
    const START_PAGE = 1;
    const PAGE_SIZE_MOBILE = 10;

    const STATUS_SPECIAL_TAG = 1;
    
    const LIMIT_FEATURED = 3;
    //const LIMIT_FEATURED_STORIES = 10;
    const LIMIT_SPECIAL_TAG = 1;
    
    // SEO config
    const TITLE_SEO_PAGING_FORMAT = '%s - Page %d of %s';

    // GroupBox to manual set Posts/Tags in a box
    const GROUP_BOX_HOME_FEATURE = 1;
    const GROUP_BOX_TOP_TAGS = 2;
    const GROUP_BOX_HOT_TOPICS = 3;
    const GROUP_BOX_TOP_REVIEWS = 4;
    const GROUP_BOX_SEO = 5;

    // Top Home featured posts
    const PAGE_TO_SHOW_VIEW_MORE = 3;
    const HOME_TOP_FEATURED_POSTS_LIMIT = 4;
    const HOME_STREAM_PAGE_SIZE = 10;
    const HOME_FEATURED_STORY_PAGE_SIZE = 1;
    const HOME_FEATURED_STORY_FIRST_PAGE_SIZE = 2;
    const HOME_STREAM_FIRST_PAGE_SIZE = 20;
    const HOME_FEATURED_POSITION_INJECT = 5;
    const CACHE_HOME_FEATURED_POST = 'CACHE:HOME:FEATURED:POST';
    const CACHE_HOME_STREAM_POST_PAGE = 'CACHE:HOME:STREAM:POST:%s';
    const HOME_BOX_LINK_LIMIT = 6;
    const CACHE_HOME_BOX_LINK = 'CACHE:HOME:BOX:LINK';
    const HOME_TRENDING_TAGS_LIMIT = 5;
    const CACHE_HOME_TAGS_TRENDING = 'CACHE:HOME:TAGS:TREDING';
    const HOME_GAME_TAGS_LIMIT = 5;
    const CACHE_HOME_TAGS_GAME = 'CACHE:HOME:TAGS:GAME';
    const HOME_TRENDING_STORY_LIMIT = 3;
    const CACHE_HOME_TRENDING_STORY = 'CACHE:HOME:TRENDING_STORY';
    const HOME_REVIEWS_LIMIT = 10;
    const REVIEW_CATE_ID = 6;
    const CACHE_HOME_REVIEWS = 'CACHE:HOME:REVIEWS';
    const HOME_HOT_VIDEO_LIMIT = 5;
    const VIDEO_TOP_PAGE_LIMIT = 16;
    const VIDEO_PAGE_LIMIT = 12;
    const START_VIDEO_PAGE = 0;
    const CACHE_HOME_HOT_VIDEO = 'CACHE:HOME:HOT_VIDEO';

    // Status Post 0: Draft, 1: Pending, 2: Published, 3: Trash
    const POST_DRAFT = 0;
    const POST_PENDING = 1;
    const POST_PUBLISHED = 2;
    const POST_TRASH = 3;

    // Focus status post
    const FOCUS_STATUS_FEATURED_STORY = 3;
    const FOCUS_STATUS_TRENDING_STORY = 4;

    // Type post
    const TYPE_POST = 1;
    const TYPE_PAGE = 0;

    // Post Service
    const CACHE_POST_SLUG_ID = '_CACHE:POST:SLUG:%s:ID:%s';
    const CACHE_RELATED_POSTS = '_CACHE:RELATED:POSTS:%s';
    const CACHE_RELATED_ARTICLES = '_CACHE:RELATED:ARTICLES:%s';
    const CACHE_FEATURED_STORIES = '_CACHE:FEATURED:STORIES:%s';

    // post detail AMP
    const CACHE_AMP_DETAIL_POST_SLUG_ID = '_CACHE:AMP:POST:SLUG:%s:ID:%s';
    const CACHE_AMP_DETAIL_RELATED_POSTS = '_CACHE:AMP:RELATED:POSTS:%s';
    const CACHE_AMP_DETAIL_RELATED_ARTICLES = '_CACHE:AMP:RELATED:ARTICLES:%s';


    // Limit related post
    const LIMIT_RELATED = 3;

    // Limit featured stories
    const LIMIT_FEATURED_STORIES = 10;

    // Video
    const VIDEO_PUBLISHED_STATUS = 1;
    const VIDEO_HOME_FOCUS = 1;
    const TITLE_LENGTH_VIDEO_LIST = 70;
    const TITLE_LENGTH_VIDEO_FIRST = 60;
    const TITLE_LENGTH_VIDEO_LIST_BOTTOM = 45;
    const FOUR_TOP_RIGHT_VIDEO = 15;

    // RSS
    const FEED_RSS_LIMIT = 18;
    const TABLE_RSS_FEED = '_CACHE:RSS:FEED';
    const TABLE_RSS_CATE_FEED = '_CACHE:RSS:CATE:FEED:PARENT%s:CATE%s';
    const TABLE_RSS_TAGS_FEED = '_CACHE:RSS:TAGS:FEED:SLUG%s';
    const TABLE_RSS_AUTHOR_FEED = '_CACHE:RSS:AUTHOR:FEED:%s';

    //
    const TABLE_ARTICLE_DETAIL_BY_SLUG = '_CACHE:ARTICLE:DETAIL:SLUG:%s';


    // Sync queue constants
    const QUIZ_KEY_QUEUE = 'Queue:Quiz:playcount1';
    const FACEBOOK_VIEW_KEY_QUEUE = 'Queue:Facebook:Views';
    const ARTICLE_VIEW_QUEUE = 'Queue:Article:view1';
    const FOLLOWING_KEY_QUEUE = 'Queue:Follow:action';
    const COOKIE_NAME = 'dXNlcl92b3Rl_';
    const FACEBOOK_LIKE_KEY_QUEUE = 'Queue:Facebook:Likes';
    const GOOGLE_LIKE_KEY_QUEUE = 'Queue:Google:like1';

    // SEARCH
    const SEARCH_POST_LIMIT = 10;
    const SEARCH_FEATURED_STORY_LIMIT = 10;
    const POST_LIST_TITLE_LENGTH = 95;
    const POST_LIST_SAPO_LENGTH = 235;
    const AUTOLOAD_NUMBER = 3;
    
    // SITEMAP
    const CACHE_SITEMAP_GLOBAL = '_CACHE:COMPONENT:SITEMAP:GLOBAL';
    const CACHE_SITEMAP_CATEGORIES = '_CACHE:COMPONENT:SITEMAP:CATEGORIES';
    const CACHE_SITEMAP_TAG_INDEX = '_CACHE:COMPONENT:SITEMAP:TAGS';
    const CACHE_SITEMAP_TAGS = '_CACHE:COMPONENT:SITEMAP:TAGS:PAGE:%s';
    const CACHE_SITEMAP_ARTICLES = "_CACHE:COMPONENT:SITEMAP:ARTICLE:DATE:%s";
    
    const SITEMAP_TAG_PERPAGE = 150;
    
    // Mobile suffix
    const MOBILE = 'MOBILE:';
    const WEBSITE = 'WEBSITE:';

    /******************************************** MOBILE ************************************************/
    // Mobile suffix
    const M_HOME_TOP_FEATURED_POSTS_LIMIT = 3;
    const M_HOME_STREAM_FIRST_PAGE_SIZE = 20;
    const M_HOME_STREAM_PAGE_SIZE = 10;
    const M_HOME_FEATURED_POSITION_INJECT = 3;
    const M_VIDEO_PAGE_LIMIT = 7;
    const M_HOME_TRENDING_TAGS_LIMIT = 50;
    const M_HOME_FEATURED_STORY_FIRST_PAGE_SIZE = 2;
    const M_HOME_FEATURED_STORY_PAGE_SIZE = 1;
    const M_HOME_TOP_VIDEO_LIMIT = 7;
    const M_TITLE_FIRST_VIDEO_LIMIT = 80;
    const M_TITLE_LIST_VIDEO_LIST = 35;
    const M_VIDEO_AJAX_PAGE_LIMIT = 10;

    // size image - Home page
    const M_FIRST_TOP_FEATURED_SIZE = '480x254';
    const M_OTHER_TOP_FEATURED_SIZE = '225x233';
    const M_SIZE_IMG_HOME_TRENDING_TAGS = '180x118';
    const M_HOME_AVATAR_LIST_SIZE = '218x143';
    const M_HOME_FEATURED_STORY_SIZE = '450x268';

    // size image - video page
    const M_FIRST_TOP_VIDEO_SIZE = '480x254';
    const M_LIST_VIDEO_SIZE = '218x143';

    // config cut Number Characters
    const CUT_NUMBER_CHARACTERS_DEFAULT = 0;
    const CUT_NUMBER_CHARACTERS_LIST_SUMMARY = 155;
    const CUT_NUMBER_CHARACTERS_FEATURED_TOP_SUMMARY = 300;
    const CUT_NUMBER_CHARACTERS_FEATURED_TOP_TITLE = 65;
    const CUT_NUMBER_CHARACTERS_FEATURED_TOP_TITLE_V2 = 40;
    
    // config cut Number Characters Mobile
    const M_CUT_NUMBER_CHARACTERS_LIST_TITLE = 100;
    const M_CUT_NUMBER_CHARACTERS_LIST_SUMMARY = 70;
    const M_CUT_NUMBER_CHARACTERS_FEATURED_TOP_TITLE = 40;
    const M_CUT_NUMBER_CHARACTERS_FEATURED_TOP_SUMMARY = 150;
    const M_CUT_NUMBER_CHARACTERS_FEATURED_TOP_TITLE_V2 = 70;
    
    // size image link share
    const SIZE_IMG_LINK_SHARE_POST_DETAIL = '620x324';
    
    // config environment cache clear
    const CONFIG_ENVIRONMENT = 'dev';
    const CONFIG_NO_WARMUP = 1;
    const CONFIG_MESSAGE_CACHE = '// Clearing the cache for the dev environment with debug // true // Cache is fresh. // Finished [OK] Cache for the "dev" environment (debug=true) was successfully cleared.';
}
