<?php
/**
 * author: AnhPT4
 * Date: 3/18/2019
 * Time: 4:43 PM
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use App\Utils\Constants;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\KernelInterface;

class CacheManagerController extends BaseController
{
    const USERNAME = 'india';
    
    /**
     * author: AnhPT4
     * date: 2019-41-19 9:41 AM
     * @param Request $request
     * @param KernelInterface $kernel
     * @return Response
     * @throws \Exception
     */
    public function cacheManager(Request $request, KernelInterface $kernel, Session $session)
    {
        $CheckFormValidate = $this->CheckFormValidate($request, $session);
        if ($CheckFormValidate === true) {
            $type = $request->get('type', 0);
            if ($type == 1) // clear cache data or pages
                $content = $this->CheckClearCacheDataPage($request);
            else // clear cache layout
                $content = $this->CheckClearCacheLayout($request, $kernel);
            
            $form = 1; // form clear cache
        } else {
            $content = '';
            $form = 2; // form login
        }
        $url = $this->generateUrl('cache_management');
        $seo = $this->buildPagingMeta($url, 'Cache Clear');
        return $this->render('default/cache-manager.html.twig', [
            'url' => $url,
            'seo' => $seo,
            'form' => $form,
            'content' => $content
        ]);
    }
    
    /**
     * Check Clear Cache Data or Page
     * author: AnhPT4
     * date: 2019-32-29 17:32 PM
     * @param Request $request
     * @return string|void
     * @throws \Exception
     */
    public function CheckClearCacheDataPage(Request $request)
    {
        $typeCache = $request->get('type_cache', 1);
        if ($typeCache == 1) {
            $cacheService = $this->getCacheProvider(Constants::SERVER_CACHE_ARTICLE);
            return $cacheService->flush();
        } elseif ($typeCache == 2) {
            $cacheService = $this->getCacheProvider(Constants::SERVER_CACHE_FULL_PAGE);
            return $cacheService->flush();
        } else {
            return '';
        }
    }
    
    /**
     * Check Clear Cache
     * author: AnhPT4
     * date: 2019-8-19 15:8 PM
     * @param Request $request
     * @param KernelInterface $kernel
     * @return string
     * @throws \Exception
     */
    public function CheckClearCacheLayout(Request $request, KernelInterface $kernel)
    {
        $checkClearCache = $request->get('check-clear-cache');
        if (empty($checkClearCache) || $checkClearCache != 1)
            return '';
        
        $env = $request->get('env', Constants::CONFIG_ENVIRONMENT);
        $no_warmup = $request->get('no-warmup', Constants::CONFIG_NO_WARMUP);
        
        $application = new Application($kernel);
        $application->setAutoExit(false);
        $input = new ArrayInput([
            'command' => 'cache:clear',
            '--env' => $env,
            '--no-warmup' => !empty($no_warmup) && $no_warmup == 1 ? true : false,
        ]);
        
        // You can use NullOutput() if you don't need the output
        $output = new BufferedOutput();
        $application->run($input, $output);
        
        // return the output, don't use if you used NullOutput()
        $content = $output->fetch();
        
        // return new Response(""), if you used NullOutput()
        //return new Response($content);
        //if ($content == Constants::CONFIG_MESSAGE_CACHE)
        return $content;
    }
    
    /**
     * Check Form Validate
     * author: AnhPT4
     * date: 2019-38-19 13:38 PM
     * @param Request $request
     * @param Session $session
     * @return bool
     */
    public function CheckFormValidate(Request $request, Session $session)
    {
        $session->start();
        $login = $session->get('login');
        if (empty($login) || $login === false) {
            $userFix = self::USERNAME;
            $passFix = $this->getParameter('clear_cache_password');
            
            $username = $request->get('username');
            $password = $request->get('password');
            
            if (($username == $userFix) && ($password == $passFix)) {
                // set and get session attributes
                $session->set('login', true);
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }
}