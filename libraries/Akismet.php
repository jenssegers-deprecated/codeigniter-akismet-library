<?php
/**
 * @name CodeIgniter Akismet Anti Spam Library
 * @author Jens Segers
 * @link http://www.jenssegers.be
 * @license MIT License Copyright (c) 2012 Jens Segers
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

if (!defined("BASEPATH"))
    exit("No direct script access allowed");

class Akismet {
    
    public $error;
    protected $blog, $api_key;
    
    function __construct($config = array()) {
        $this->initialize($config);
    }
    
    function initialize($config = array()) {
        foreach ($config as $key => $val) {
            $this->$key = $val;
        }
        
        if (!$this->blog) {
            $ci = &get_instance();
            $this->blog = $ci->config->base_url();
        }
    }
    
    function verify() {
        $params = array();
        $params['key'] = $this->api_key;
        $params['blog'] = $this->blog;
        
        $response = $this->request('https://rest.akismet.com/1.1/verify-key', $params);
        
        if (strtolower(trim($response)) == 'valid') {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    function check($author, $email, $content) {
        $params = array();
        $params['blog'] = $this->blog;
        $params['user_ip'] = $_SERVER['REMOTE_ADDR'];
        $params['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
        
        if (isset($_SERVER['HTTP_REFERER'])) {
            $params['referrer'] = $_SERVER['HTTP_REFERER'];
        }
        
        $params['comment_author'] = $author;
        $params['comment_author_email'] = $email;
        $params['comment_content'] = $content;
        
        $response = $this->request('https://' . $this->api_key . '.rest.akismet.com/1.1/comment-check', $params);
        
        if (strtolower(trim($response)) == 'true') {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    private function request($url, $params) {
        $params = http_build_query($params, NULL, '&');
        $curl = curl_init();
        
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
        curl_setopt($curl, CURLOPT_POST, TRUE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded', 'Content-length: ' . strlen($params)));
        
        $response = curl_exec($curl);
        
        if ($response === FALSE) {
            $this->error = curl_error($curl);
            curl_close($curl);
            return FALSE;
        }
        
        curl_close($curl);
        return $response;
    }

}