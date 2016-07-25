<?php
/**
 * DNS-prefetch
 */
\Larakit\Event\Event::listener('lk-page::meta-tags', function (sfEvent $e, $parameters) {
    if(LaraPage::getDnsPrefetch()) {
        $parameters[] = '<!-- DNS prefetch -->';
        $parameters[] = HtmlMeta::setAttribute('http-equiv', 'x-dns-prefetch-control')->setAttribute('content', 'on');
        foreach(LaraPage::getDnsPrefetch() as $url) {
            $parameters[] = HtmlLink::setRel('dns-prefetch')->setHref($url);
        }
    }

    return $parameters;
});

/**
 * Opengraph
 */
\Larakit\Event\Event::listener('lk-page::meta-tags', function (sfEvent $e, $parameters) {
    $src = LaraPage::getImage();
    if($src) {
        $parameters[] = '<!-- Opengraph image -->';
        $parameters[] = HtmlLink::setRel('image_src')->setAttribute('href', $src);
        $parameters[] = HtmlMeta::setProperty('og:image')->setAttribute('href', $src);
    }

    return $parameters;
});

/**
 * Charset
 */
\Larakit\Event\Event::listener('lk-page::meta-tags', function (sfEvent $e, $parameters) {
    $charset = LaraPage::getCharset();
    if($charset) {
        $parameters[] = '<!-- Charset -->';
        $parameters[] = HtmlMeta::setAttribute('charset', $charset);
    }

    return $parameters;
});

/**
 * Favicons
 */
\Larakit\Event\Event::listener('lk-page::meta-tags', function (sfEvent $e, $parameters) {
    if(LaraPage::getFavicon() || LaraPage::getAppleTouchs()) {
        $parameters[] = '<!-- Favicons -->';
        if(LaraPage::getFavicon()) {
            $parameters[] = HtmlLink::setHref(LaraPage::getFavicon());
        }
        if(LaraPage::getAppleTouchs()) {
            foreach(LaraPage::getAppleTouchs() as $size => $url) {
                $link = HtmlLink::setRel('apple-touch-icon')->setHref($url);
                if($size) {
                    $link->setAttribute('sizes', $size . 'x' . $size);
                }
                $parameters[] = $link;
            }
        }
    }

    return $parameters;
});

/**
 * Viewport
 */
\Larakit\Event\Event::listener('lk-page::meta-tags', function (sfEvent $e, $parameters) {
    $viewport = LaraPage::getViewport();
    if($viewport) {
        $parameters[] = '<!-- Viewport -->';
        $parameters[] = HtmlMeta::setName('viewport')->setContent($viewport);
    }

    return $parameters;
});

/**
 * Generator
 */
\Larakit\Event\Event::listener('lk-page::meta-tags', function (sfEvent $e, $parameters) {
    $generator = LaraPage::getGenerator();
    if($generator) {
        $parameters[] = '<!-- Generator -->';
        $parameters[] = HtmlMeta::setName('generator')->setContent($generator);
    }

    return $parameters;
});
