<?php namespace ProcessWire;

/**
 *
 * https://processwire.com/docs/tutorials-old/quick-start/navigation/
 * Usage => <?=simpleNav($pages->get("/"))?>
 *
 * @param Page|null $root
 *
 */
function simpleNav($root = null)
{
    // $home = pages()->get("/");
    $children = $root->children();
    $children->prepend($root);
    $out = '';
    foreach ($children as $child) {
        $class = $child->id == wire('page')->id ? 'active' : 'no-active';
        $out .= "<li class='{$class}'><a class='nav-item' href='{$child->url}'>{$child->title}</a></li>";
    }
    return $out;
}

/**
 *
 * @param Page|null $root
 * @param array|null $opt
 *
 */
function burgerNav($root = null, $opt = null)
{
    $children = $root->children();
// $children->prepend($root);
    $out = '';
    $out .="
    <!-- Navigation -->
    <ul class='b-nav'>";

    foreach ($children as $child) {
        $class = $child->id == wire('page')->id ? 'b-link--active' : 'no-active';
        $out .= "<li>
                  <a class='b-link {$class}' href='{$child->url}'>
                     {$child->title}
                   </a>
                 </li>";
    }
        $out .= "</ul>";

    $out .= "<!-- Burger-Icon -->
    <div class='b-container'>
        <div class='b-menu'>
          <div class='b-bun b-bun--top'></div>
          <div class='b-bun b-bun--mid'></div>
          <div class='b-bun b-bun--bottom'></div>
        </div>";

    if (isset($opt['logoUrl'])) {
        $alt = isset($opt['alt']) ? $opt['alt'] : '';
        $out .="<!-- Burger-Logo -->
    <a class='logo' href='{$root->httpUrl}'>
        <img src='{$opt['logoUrl']}'
        alt='{$alt}'
        width='91' height='49'/>
    </a>";
    } elseif (isset($opt['brand'])) {
        $out .= "<a href='{$root->httpUrl}' class='b-brand'>" . $opt['brand'] . "</a>";
    }

    $out .= "</div><!-- /.b-container -->";

    return $out;
}

/**
 *
 * @param Page $root
 * @param Page $page
 *
 */
function linkTag($root, $page)
{

    // If Multi Language Modules activate
    if (!$page->getLanguages()) {
        return '';
    }
  
    $out = '';
    // handle output of 'hreflang' link tags for multi-language
    // this is good to do for SEO in helping search engines understand
    // what languages your site is presented in
    foreach (languages() as $language) {
        // if this page is not viewable in the language, skip it
        if (!$page->viewable($language)) {
            continue;
        }
        // get the http URL for this page in the given language
        $url = $page->localHttpUrl($language);
        // hreflang code for language uses language name from homepage
        $hreflang = $root->getLanguageValue($language, 'name');
  
        // if($hreflang == 'home') $hreflang = page()->ts['languageCode'];
  
        // output the <link> tag: note that this assumes your language names are the same as required by hreflang.
        $out .= "\t<link rel='alternate' hreflang='$hreflang' href='$url' />\n";
    }
    return $out;
}
  
  /**
   *
   * @param Page $page
   * @param Page $root
   *
   */
function langMenu($page, $root)
{
    // If Enable Multilanguage Modules
    if (!page()->getLanguages()) {
        return '';
    }
    $out = '';
    // language switcher / navigation
    $out .= "<ul class='lang-menu grid' role='navigation'>";
    // Start Loop
    foreach (languages() as $language) {
    // is page viewable in this language?
        if (!$page->viewable($language)) {
            continue;
        }
  
        if ($language->id == user()->language->id) {
            $out .= "<li class='active'>";
        } else {
            $out .= "<li>";
        }
  
        $url = $page->localUrl($language);
        $hreflang = $root->getLanguageValue($language, 'name');
        $out .= "<a hreflang='$hreflang' href='$url'>$language->title</a></li>";
    }
  
    $out .= "</ul>";
  
    return $out;
}

/**
 *
 * @param Page|PageArray|null $page
 *
 */
function breadCrumb($page = null)
{

    if ($page == null) {
        return '';
    }

      $out = '';
            // breadcrumbs are the current page's parents
    foreach ($page->parents() as $item) {
        if ($item->name == 'home') {
            $out .= "<span class='home'><a href='$item->url'>
            <i data-feather='home'
            width=25 height=25
            stroke-width=1
            color=#9b4dca>
            </i>
            </a> > </span> ";
        } else {
            $out .= "<span><a href='$item->url'>$item->title</a></span> > ";
        }
    }
        // optionally output the current page as the last item
        $out .= $page->id != 1  ? "<span>$page->title</span>" : '';
        return $out;
}

/**
 *
 * @param Page|PageArray|null $page
 * @param array|null $opt
 *
 */
function pageChildren($page = null, $opt = null)
{

    if (!count($page->children)) {
        return '';
    }

// Reset variables
    $out = '';
    $random = '';

// Default Text
    $txt = isset($opt['txt']) ? $opt['txt'] : page()->ts['morePages'];

// Limit Items
    $limit = isset($opt['limit']) ? $opt['limit'] : 8;

// Random Items
    if (isset($opt['random']) && $opt['random'] == true) {
        $random = "sort=random,";
    }

    $out .= "<h3>$txt</h3>";

    $out .= "<ul class='page-children'>";

    foreach ($page->children("limit=$limit,$random start=0") as $child) {
        $out .= "<li><a href='$child->url'>$child->title</a></li>";
    }

    $out .= '</ul>';

    return $out;
}

/**
 *
 * @param Page $page
 * // All Sizes 'thumb','small','medium','large', 'full'(default)
 * @param string $size
 *
 */
function getImage($page, $size = 'full')
{
    // View a replacement image from https://picsum.photos/
    if (page()->opt['demoImage']) {
        return imgDemo($page);
    } else {
        return $page->render('images', "img-$size");
    }
}

/**
 *
 * Smart SEO
 *
 * @param Page $page
 *
 */
function smartSeo($page)
{

// Check if Smart Seo is enabled
    if (!page()->opt['smartSeo']) {
        return '';
    }

// Reset variables
    $out = '';
    $tw_image = '';

// No index
    if (page()->check_1) {
        echo "\t<meta name='robots' content='noindex'>\n";
    }
// https://processwire.com/blog/posts/processwire-2.6.18-updates-pagination-and-seo/
    if (input()->pageNum > 1) {
        echo "\t<meta name='robots' content='noindex,follow'>\n";
    }
// https://weekly.pw/issue/222/
    if (config()->pagerHeadTags) {
        echo "\t" . config()->pagerHeadTags . "\n";
    }
    
// https://processwire.com/blog/posts/processwire-2.6.18-updates-pagination-and-seo/#using-a-pagination-view-all-page
// specify scheme and host statically rather than from $page->httpUrl
// $canonicalURL = 'https://www.domain.com' . $page->url;
    if (page()->opt['cannonicalUrl']) {
        $canonicalURL = page()->opt['cannonicalUrl'] . $page->url;
    } else {
        $canonicalURL = $page->httpUrl;
    }
    
// if on a pagination, include that as part of your canonical URL
    if (input()->pageNum > 1) {
        $canonicalURL .= config()->pageNumUrlPrefix . input()->pageNum;
    }
         
// Get locale
    $locale = page()->ts['locale'];
// Site Name
    $siteName = page()->opt['optionsPage']->headline ?: page()->ts['siteName'];
// Basic Meta
        $out .= "\t<meta property='og:locale' content='{$locale}'/>\n";
        $out .= "\t<meta id='og-title' property='og:title' content='{$page('headline|title')}'/>\n";
        $out .= "\t<meta id='og-desc' property='og:description' content='{$page->summary}'>\n";
        $out .= "\t<meta id='og-type' property='og:type' content='website'/>\n";
        $out .= "\t<meta id='og-url' property='og:url' content='{$canonicalURL}'/>\n";
        $out .= "\t<meta property='og:site_name' content='{$page->ts['siteName']}'/>\n";

// If Page Images
    if ($page->images && count($page->images)) {
        // Get large size ( 1200px )
            $large = page()->opt['large'];
        // Get image width
            $img = $page->images->first()->width($large);
        // Show Image
            $out .= "\t<meta id='og-image' property='og:image' content='{$img->httpUrl()}'/>\n";
        // Image Size
            $out .= "\t<meta property='og:image:width' content='$img->width'/>\n";
            $out .= "\t<meta property='og:image:height' content='$img->height'/>\n";
        // TWITTER IMAGE
            $tw_image = "\t<meta name='twitter:image' content='{$img->httpUrl()}'/>\n";
    }

// Simple Twitter Card
    if (page()->opt['enableTwitter']) {
        $tw_summary = page()->opt['largeImage'] ? 'summary_largeImage' : 'summary';

        $out .= "\t<meta name='twitter:card' content='{$tw_summary}'/>\n";
        $out .= "\t<meta name='twitter:title' content='{$page('headline|title')}'/>\n";
        $out .= "\t<meta name='twitter:description' content='{$page->summary}'/>\n";
        $out .= "$tw_image";
    }

// Cannonical Link
    $out .= "\t<link rel='canonical' href='{$canonicalURL}'/>\n";
        
    return $out;
}

/**
 *
 * Google Webmaster Tools Verification Code
 *
 * @param string|null $code
 *
 */
function gwCode($code = null)
{

    if ($code) {
        return "<meta name='google-site-verification' content='$code' />\n";
    }
}

/**
 *
 * // Basic Example ( also example is inside /render/grid.php )
 *  echo icon('user',[
 *  'height' => 40,
 *  'width' => 40,
 *  'stroke' => 4,
 *  'color' => '#201f27',
 *  'text' => __('Hello'),
 *  'html_el' => 'h1', // html elements like h1 h2 h3 h4 h5 span p ...
 *  // 'before' => true,
 *  'url'=>  'https://feathericons.com/',
 *  't_url' => true, // <a href='#' target='_blank'
 *  'class' => 'custom-class',
 *  ]);
 *
 * @param string|null $icon
 * @param array|null $opt
 *
 */
function icon($icon = null, $opt = null)
{

    if ($icon == null) {
        return '';
    }

// Reset variables

    $out = '';

    // Get options
        $txt = isset($opt['txt']) ? $opt['txt'] : '';
        $url = isset($opt['url']) ? $opt['url'] : '#';
        $t_blank = isset($opt['t_blank']) && $opt['t_blank'] == true ? "target='_blank'" : '';
        $width = isset($opt['width']) ? $opt['width'] : 25;
        $height = isset($opt['height']) ? $opt['height'] : 25;
        $color = isset($opt['color']) ? $opt['color'] : '#303438';
        $stroke = isset($opt['stroke']) ? $opt['stroke'] : 1;
        $class = isset($opt['class']) ? $opt['class'] : 'font-class';
        $a_class = isset($opt['a_class']) ? $opt['a_class'] : 'a-class';

    // custom Url
    if (isset($opt['url'])) {
        $out .= "<a class='$a_class' href='$url' $t_blank>";
    }

    // html elements like h1 h2 h3 h4 h5 span p ...
        $out .= isset($opt['html_el']) ? "<{$opt['html_el']} class='$class'>" : '';

    // Show Custom Text Before
    if (isset($opt['before']) && $opt['before'] == true) {
        $out .= $txt;
    }
        
            $out .= "<i data-feather='$icon' 
            width=$width 
            height=$height 
            stroke-width=$stroke
            color=$color>
            </i>";

    // Show Custom Text
    if (!isset($opt['before'])) {
        $out .= $txt;
    }

    // End custom html elements
        $out .= isset($opt['html_el']) ? "</{$opt['html_el']}>" : '';

    // /End custom url
    if (isset($opt['url'])) {
            $out .= "</a>";
    }
    
    return $out;
}

/**
 *
 * @param array $fonts
 *
 */
function googleFonts(array $fonts)
{

// Implode to format => 'Roboto','Montserrat','Limelight','Righteous'
    $font_family = "'" . implode("','", $fonts) . "'";

    return"<script>
/* ADD GOOGLE FONTS WITH WEBFONTLOADER ( BETTER PAGESPEED )
    https://github.com/typekit/webfontloader
    https://fonts.google.com/?subset=latin-ext&selection.family=Comfortaa|Limelight|Montserrat
*/
WebFontConfig = {
        google: {
        families: [$font_family]
    }
};
    (function(d) {
        var wf = d.createElement('script'), s = d.scripts[0];
        wf.src = 'https://ajax.googleapis.com/ajax/libs/webfont/1.6.26/webfont.js';
        wf.async = true;
        s.parentNode.insertBefore(wf, s);
    })(document);

</script>";
}

/**
 *
 * START PAGINATION https://processwire.com/api/modules/markup-pager-nav/
 *
 * @param Page $items
 * @param string|null $class
 *
 */
function basicPagination($items, $class = null)
{

    return $items->renderPager(array(
        'nextItemLabel' => page()->ts['next'],
        'previousItemLabel' => page()->ts['prev'],
        'listMarkup' => "<ul class='MarkupPagerNav $class'>{out}</ul>",
        'itemMarkup' => "<li class='{class}'>{out}</li>",
        'linkMarkup' => "<a href='{url}'><span>{out}</span></a>",
        'numPageLinks' => 10,
        'currentItemClass' => 'active'
    ));
}

/**
 * Given a group of pages, render a <ul> navigation tree
 *
 * This is here to demonstrate an example of a more intermediate level
 * shared function and usage is completely optional. This is very similar to
 * the renderNav() function above except that it can output more than one
 * level of navigation (recursively) and can include other fields in the output.
 *
 * @param array|PageArray $items
 * @param int $maxDepth How many levels of navigation below current should it go?
 * @param string $fieldNames Any extra field names to display (separate multiple fields with a space)
 * @param string $class CSS class name for containing <ul>
 * @return string
 *
 */
function renderNavTree($items, $maxDepth = 0, $fieldNames = '', $class = 'nav')
{

    // if we were given a single Page rather than a group of them, we'll pretend they
    // gave us a group of them (a group/array of 1)
    if ($items instanceof Page) {
        $items = array($items);
    }

    // $out is where we store the markup we are creating in this function
    $out = '';

    // cycle through all the items
    foreach ($items as $item) {
        // markup for the list item...
        // if current item is the same as the page being viewed, add a "current" class to it
        $out .= $item->id == wire('page')->id ? "<li class='current'>" : "<li>";

        // markup for the link
        $out .= "<a href='$item->url'>$item->title</a>";

        // if there are extra field names specified, render markup for each one in a <div>
        // having a class name the same as the field name
        if ($fieldNames) {
            foreach (explode(' ', $fieldNames) as $fieldName) {
                $value = $item->get($fieldName);
                if ($value) {
                    $out .= " <div class='$fieldName'>$value</div>";
                }
            }
        }

        // if the item has children and we're allowed to output tree navigation (maxDepth)
        // then call this same function again for the item's children
        if ($item->hasChildren() && $maxDepth) {
            if ($class == 'nav') {
                $class = 'nav nav-tree';
            }
            $out .= renderNavTree($item->children, $maxDepth-1, $fieldNames, $class);
        }

        // close the list item
        $out .= "</li>";
    }

    // if output was generated above, wrap it in a <ul>
    if ($out) {
        $out = "<ul class='$class'>$out</ul>\n";
    }

    // return the markup we generated above
    return $out;
}

/**
 *
 * // Example
 * echo cookieBanner(
 * [
 *    'message' => page()->ts['privacyMessage'],
 *    'dismiss' => page()->ts['goIt'],
 *    'link' => page()->ts['learnMore'],
 *    'href' => page()->opt['privacyPage']->url
 * ]);
 *
 * @param array $info
 *
 */
function cookieBanner(array $info)
{
    
    $message = isset($info['message']) ? $info['message'] : __('Privacy & Cookies Policy.');
    $dismiss = isset($info['dismiss']) ? $info['dismiss'] : __('Got it!');
    $link = isset($info['link']) ? $info['link'] : __('Learn More');
    $href = isset($info['href']) ? $info['href'] : __('Privacy & Cookies Policy.');

    return "<link rel='stylesheet' type='text/css' href='//cdnjs.cloudflare.com/ajax/libs/cookieconsent2/3.1.0/cookieconsent.min.css' />
<script defer src='//cdnjs.cloudflare.com/ajax/libs/cookieconsent2/3.1.0/cookieconsent.min.js'></script>
<script>
window.addEventListener('load', function(){
window.cookieconsent.initialise({
  'palette': {
    'popup': {
      'background': 'rgba(19, 20, 23, 0.71)'
    },
    'button': {
      'background': '#14a7d0'
    }
  },
  'theme': 'classic',
  'position': 'bottom-right',
  'content': {
    'message': '$message',
    'dismiss': '$dismiss',
    'link': '$link',
    'href': '$href'
  }
})});
</script>";
}

/**
 *
 * https://developers.google.com/analytics/devguides/collection/analyticsjs/
 *
 * @param string $code Google Analytics Tracking Code
 *
 */
function gAnalitycs($code)
{
    return "\n
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src='https://www.googletagmanager.com/gtag/js?id=UA-{$code}'></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'UA-{$code}');
</script>";
}

/**
 *
 * @param Page|null $item
 * @param array $opt
 *
 */
function imgDemo($item, $opt = null)
{

    $out = '';
    $width = isset($opt['width']) ? $opt['width'] : '640';
    $height = isset($opt['height']) ? $opt['height'] : '420';

    if (count($item->images)) {
        $out .="<img class='center lazy'
        data-src='{$item->images->first->url}'
        alt='{$item->description}'
        width='$width' height='$height'>";
    } else {
    // Home Page Random images from https://picsum.photos/
        if (isset($opt['random']) && $opt['random'] == true) {
            $width = '3' . rand(1, 4) . '0';
            $height = '2' . rand(5, 9) . '0';
        }

        $out .="<img class='center lazy'
        data-src='https://picsum.photos/$width/$height'
        alt='$item->title'
        width='$width' height='$height'>";
    }

    return $out;
}

/**
 *
 * Prev Next Button
 * Basic Example echo prNx($page, 'grid')
 *
 * @param Page|null $item
 * @param string $class
 *
 */
function prNx($item = null, $class = 'prev-next')
{

// Prev Next Button
    $p_next = $item->next();
    $p_prev = $item->prev();

    $out = '';

    $out .= "<div class='$class'>";

// link to the prev blog post, if there is one
    if ($p_prev->id) {
// Get function icon()
        $out .= icon('arrow-left', [
        'txt' => $p_prev->title,
        'url' => $p_prev->url,
        'a_class' => 'm-1 button button-outline',
        ]);
    }

// link to the next blog post, if there is one
    if ($p_next->id) {
// Get function icon()
        $out .= icon('arrow-right', [
        'txt' => $p_next->title,
        'url' => $p_next->url,
        'before' => true,
        'a_class' => 'm-1 button button-outline',
        ]);
    }

    $out .= '</div>';

    return $out;
}


/**
 *
 * TRASH DEMO DATA => USAGE: trashDemoData($trash = true);
 *
 * @param  bool $trash
 *
*/
function trashDemoData($trash = false)
{
    // IF TRUE
    if ($trash == true) {
        // GET ID ALL PAGES TO TRASH
        $arr_p = [
            // '1029', // About Page
            '1030','1031','1032','1033', // About Children
            // '1023', // News
            '1024','1025','1026', // News Children
            // '1016', // Basic Page
            '1018','1021', // Basic Page Children
        ];
    // Add pages to trash
        foreach ($arr_p as $key) {
            $trash_p = pages()->get($key);
        // IF PAGE EXSIST
            if ($trash_p->name == true) {
        // PAGE TO TRASH
                pages()->trash($trash_p);
            // OR DELETE
                // pages()->delete($trash_p);
            }
        }
    }
}
