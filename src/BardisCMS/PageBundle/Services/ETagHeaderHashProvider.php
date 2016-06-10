<?php

/*
 * PageBundle Bundle
 * This file is part of the BardisCMS.
 *
 * (c) George Bardis <george@bardis.info>
 *
 */

namespace BardisCMS\PageBundle\Services;

use Symfony\Component\HttpFoundation\Request;

class ETagHeaderHashProvider {

    /**
     * Generate a unique ETag identifier
     *
     * @param string|null $url              The url path that was requested
     * @param string $publishState          The $publishState or the requested page
     * @param \DateTime $dateLastModified   The last modified datetime of the page in CMS
     * @param string $username              Logged User username
     *
     * @return string
     */
    public function getETagHash($url, $publishState, \DateTime $dateLastModified , $username) {

        $eTagSource =<<<STRING
            {$url}
            {$publishState}
            {$dateLastModified->format('c')}
            {$username}
STRING;

        $eTagHash = hash('sha256', $eTagSource); //md5($eTagSource);

        return $eTagHash;
    }

    /**
     * Normalize the if_none_match header for for the ETag invalidation to work properly when gzip is enabled
     * more info at https://coderwall.com/p/rl6v7a/http-caching-in-symfony2-max-age-etag-gzip
     *
     * @param Request $request      The request to normalize
     *
     * @return Request
     */
    public function normalizeETagHashWithGzip(Request $request){
        $originalETag = $request->headers->get('if_none_match');
        $eTagWithoutGzip = str_replace('-gzip"', '"', $originalETag);
        $request->headers->set('if_none_match', $eTagWithoutGzip);

        return $request;
    }
}
