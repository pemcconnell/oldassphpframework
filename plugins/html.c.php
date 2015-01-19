<?php

/**
 * Static methods used for HTML manipulation and (de)?enctyption
 * 
 * @author Peter McConnell <pemcconnell@googlemail.com>
 */
class HTML {

    static private $aExistingIds = array();

    static public function cleanOutput($value, $bHtmlEntities = false) {
        $value = stripslashes($value);
        if ($bHtmlEntities) {
            $value = strip_tags($value);
            $value = htmlentities($value);
        }
        return $value;
    }

    static public function createId($s) {
        $s = self::createCleanURL($s);
        if (!in_array($s, self::$aExistingIds)) {
            self::$aExistingIds[] = $s;
            return $s;
        } else {
            return self::createIds($s . '_');
        }
    }

    static public function cleanInput($str) {
        $str = trim($str);
        $str = addslashes($str);
        return $str;
    }

    /**
     * Attempts to make URL absolute if not already.
     *
     * @param string $uri [example.com]
     * @return string $uri [http://example.com]
     */
    static public function cleanLink($uri) {
        $uri = trim($uri);
        if (!preg_match('@^http[s]?:@i', $uri)) {
            if (preg_match('@^\.*?/+.*?@', $uri)) {
                $uri = preg_replace('@^(\.*?/+)(.*)?@', BASE_HREF . '\2', $uri);
            } else {
                if (!preg_match('@^www@i', $uri)) {
                    if (strpos($uri, '.') === false) {
                        $uri = BASE_HREF . $uri;
                    } else {
                        $uri = 'http://' . $uri;
                    }
                } else {
                    $uri = 'http://' . $uri;
                }
            }
        }

        return $uri;
    }

    /**
     * absoluteURL
     *
     * Force a URL to be absolute
     *
     * @param string $url URL to be made absolute
     *
     * @return string Absolute URL
     */
    static public function absoluteURL($url) {
        $url = trim($url);
        if (!preg_match('/^http[s]?:/', strtolower($url))) {
            $url = 'http://' . $url;
        }
        return $url;
    }

    /**
     * hashpwd
     *
     * Creates a password hash from string. Not reversable
     *
     * @param string $str  String input (raw password)
     * @param string $salt Custom salt (optional)
     *
     * @return string Hashed password
     */
    static public function hashpwd($str, $salt = '') {
        if ($salt == '') {
            global $SETTINGS;
            $salt = $SETTINGS['auth']['salt'];
        }
        $str = md5(crypt($str, $salt));
        return '$1$' . $str;
    }

    /**
     * encrypt
     *
     * Creates a hash from string. Reversable
     *
     * @param string $str    String input
     * @param string $salt   Custom salt (optional)
     * @param string $pepper Custom pepper (optional)
     *
     * @return string Encrypted version of $str input
     */
    static public function encrypt($str, $salt = '', $pepper = '') {
        if ($str == '')
            return '';
        $ret = '';
        $bIsArray = false;
        if (!is_array($str))
            $str = trim($str);
        else {
            $bIsArray = true;
            $str = serialize($str);
        }

        // SET SALT AND PEPPER
        if ($salt == '' || $pepper == '') {
            global $SETTINGS;
            if ($salt == '')
                $salt = $SETTINGS['auth']['salt'];
            if ($pepper == '')
                $salt = $SETTINGS['auth']['pepper'];
        }

        // ENCRYPT
        $charset = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!-+=_';
        $count = strlen($charset);
        $e = strrev($str);
        $e = base64_encode($e);
        $e = $salt . strrev($e) . $pepper;
        $len = strlen($str);
        $l = '=' . $len;
        $ce = '';
        $cee = '';
        while ($len--) {
            $ce .= $charset[mt_rand(0, $count - 1)];
            $cee .= $charset[mt_rand(0, $count - 1)];
        }
        $e = $ce . $e . $cee . $l;
        $ret = strrev(base64_encode($e));

        if ($bIsArray)
            $ret = '!ar_!_' . $ret;
        return $ret;
    }

    /**
     * decrypt
     *
     * Decrypts a hash created from self::encrypt()
     *
     * @param string $str    String input (hash)
     * @param string $salt   Custom salt (optional)
     * @param string $pepper Custom pepper (optional)
     *
     * @return string Decrypted version of $str input
     */
    static public function decrypt($str, $salt = '', $pepper = '') {
        $str = trim($str);
        if ($str == '')
            return '';
        $bIsArray = false;
        if (strpos($str, '!ar_!_=') !== false) {
            $bIsArray = true;
            $str = substr($str, 6, strlen($str));
        }

        // SET SALT AND PEPPER
        if ($salt == '' || $pepper == '') {
            global $SETTINGS;
            if ($salt == '') {
                $salt = $SETTINGS['auth']['salt'];
            }
            if ($pepper == '') {
                $salt = $SETTINGS['auth']['pepper'];
            }
        }

        // DECRYPT
        $ret = strrev($str);
        $ret = base64_decode($ret);
        preg_match('@^(.*)\=([0-9]+)$@', $ret, $matches);
        $ret = $matches[1];
        $n = $matches[2];
        $ret = substr($ret, $n, (strlen($ret) - ($n * 2)));
        $ret = str_replace(array($salt, $pepper), '', $ret);
        $ret = strrev($ret);
        $ret = base64_decode($ret);
        $ret = strrev($ret);

        return (!$bIsArray) ? $ret : unserialize($ret);
    }

    /**
     * getFileExt
     *
     * Gets a file extension
     *
     * @param string $filename Filename
     *
     * @return string Extension
     */
    static public function getFileExt($filename) {
        return substr(strrchr($filename, '.'), 1);
    }

    /**
     * getFileName
     *
     * Gets a file name from a URL. Strips $_GET and path(optional)
     *
     * @param string $path      URL of file
     * @param bool $bIncludeExt Boolean - set to false if the extension isn't needed
     *
     * @return string Filename
     */
    static public function getFileName($path, $bIncludeExt = true) {
        $path = preg_replace('/^(.*?)\?.*?$/', '\1', $path);
        // REMOVE ANY $_GET FROM PATH
        $filename = preg_replace('/^.*?\/([^\/]+)$/', '\1', $path);
        if (!$bIncludeExt)
            $filename = preg_replace('/^(.*)\.[^\.]{2,4}$/', '\1', $filename);
        return $filename;
    }

    /**
     * createSummary()
     *
     * Strips HTML tags and trims string length to $limit if $str is longer than $limit
     *
     * @param string $str Input string
     * @param int $limit  How many characters should it allow. Defaults to 200
     *
     * @return string Summary version of $str
     */
    static public function createSummary($str, $limit = 200) {
        $str = stripslashes($str);
        $str = strip_tags($str);
        if (strlen($str) > $limit) {
            $str = substr($str, 0, ($limit - 3)) . '...';
        }
        return $str;
    }

    /**
     * createCleanURL()
     *
     * Creates URL-friendly version of string
     *
     * @param string $str Input value to be made into a URL
     *
     * @return string URL friendly version of $str
     */
    static public function createCleanURL($str) {
        $url = trim(strtolower($str));
        $url = preg_replace(array('/\s/', '/(-&amp;-|-&-)/siu', '/[^a-z0-9-_]/siu', '/[-]+/'), array('-', '-and-', '', '-'), $url);
        $url = urlencode($url);
        return $url;
    }

    /**
     * screenshot()
     *
     * Takes a screenshot of a webpage and saves it locally. Uses webshots
     *
     * @param string $url URL of webpage
     *
     * @return string path of the image file
     */
    static public function screenshot($url) {
        if (stripos($url, 'http') === false) {
            return false;
        }
        require_once BASE_PATH . 'lib' . DS . 'webshots' . DS . 'webshots.php';
        $wobj = new webshots();
        $fname = 'scrn_' . time() . '_' . rand(0, 99);
        $img = BASE_HREF . 'uploads/cms_image/' . $fname;
        if ($wobj -> url_to_image($url, $img)) {
            return $fname;
        }
        return false;
    }

    /**
     * img()
     *
     * To be called when resizing of the image is desired. Creates a new version of the image
     * and appends the dimensions to the filename. If image does not exist this function will
     * create it. Utilizes http://wideimage.sourceforge.net/.
     *
     * @param string $href Must be absolute reference to a file in uploads/cms_image/
     * @param int $maxwidth 200
     * @param int $maxheight 400
     *
     * @return string HREF of image
     */
    static public function img($href, $maxwidth = 200, $maxheight = 400) {
        global $CONSOLE;
        if ((stripos($href, BASE_HREF) === false) || (stripos($href, 'cms_image') === false) || (((int)$maxwidth < 1) && ((int)$maxheight < 1)))
            return $href;
        if ((int)$maxwidth < 1)
            $maxwidth = 2000;
        if ((int)$maxheight < 1)
            $maxheight = 2000;
        $path = str_replace('/', DS, str_replace('/uploads/', '/tmp/uploads/', str_replace(BASE_HREF, BASE_PATH, $href)));
        if (file_exists($path)) {
            $ext = HTML::getFileExt($href);
            $fname = HTML::getFileName($href, false);
            $resizehref = $fname . '_';
            if ($maxwidth > 0)
                $resizehref .= 'w' . $maxwidth;
            if ($maxheight > 0) {
                $resizehref .= 'h' . $maxheight;
            }
            $resizehref = BASE_SSLIFON_HREF . 'uploads' . DS . 'cms_image' . DS . $resizehref . '.' . $ext;
            $resizepath = str_replace(BASE_SSLIFON_HREF, BASE_PATH, $resizehref);
            $resizepath = str_replace(DS . 'uploads' . DS, DS . 'tmp' . DS . 'uploads' . DS, $resizepath);

            if (file_exists($resizepath)) {
                return $resizehref;
            } else {
                require_once BASE_PATH . 'lib' . DS . 'wideimage' . DS . 'WideImage.php';
                try {
                    $wi = new WideImage;
                    $image = $wi -> loadFromFile($path);
                    $resized = $image -> resize($maxwidth, $maxheight, 'inside', 'any');
                    $resized -> saveToFile($resizepath);
                    @chmod($resizepath, 0777);
                    return $resizehref;
                } catch (Exception $e) {
                    $CONSOLE -> error('Error resizing ' . $href);
                    return $href;
                }
            }
        } else {
            $CONSOLE -> error('Attempted to resize a file that doesnt exist:' . $href);
            return $href;
        }
    }

    /**
     * parseHTMLOutput()
     *
     * Designed to accomodate for string output that has been generated by the CMS.
     * An example of this is to replace '&' with '&amp;'
     *
     * @param string $str
     *
     * @return string Output of $str
     */
    static public function parseHTMLOutput($str) {
        $str = str_replace(' & ', ' &amp; ', $str);
        return $str;
    }

    /**
     * Create a simple video snippet.
     *
     * @param type $height   Height
     * @param type $width    Width
     * @param type $mp4       MP4 src
     * @param type $title       Title
     * @param type $poster   Poster image src
     * @param type $ogv        OGV src
     * @param type $swf         SWF src
     *
     * @return string HTML of video
     */
    static public function video($height, $width, $mp4, $title = '', $poster = '', $ogv = '', $swf = '') {
        $html = '<video width="' . $width . '" height="' . $height . '" controls>';
        $html .= '<source src="' . $mp4 . '" type="video/mp4" />';
        if ($ogv != '')
            $html .= '<source src="' . $ogv . '" type="video/ogg" />';
        // FLASH FALLBACK
        if ($swf != '') {
            $html .= '<object width="' . $width . '" height="' . $height . '" type="application/x-shockwave-flash" data="' . $swf . '">
				<!-- Firefox uses the `data` attribute above, IE/Safari uses the param below -->
				<param name="movie" value="' . $swf . '" />
				<param name="flashvars" value="controlbar=over&amp;image=' . $poster . '&amp;file=' . $mp4 . '" />
				<img src="' . $poster . '" width="' . $width . '" height="' . $height . '" alt="' . $title . '"
				    title="' . $title . '" />
			</object>';
        }
        $html .= '</video>
	    <p>	<strong>Download Video:</strong><a href="' . $mp4 . '">' . $title . ' (MP4)</a></p>';

        return $html;
    }

    /**
     * Returns an HTML vcard as a string
     *
     * @param string $givenname Forename
     * @param string $additionalname Middle name
     * @param string $familyname Surname
     * @param string $url Website
     * @param string $org Organisation
     * @param string $email Email
     * @param string $streetaddress Street Addresss 1
     * @param string $locality Locality
     * @param string $region Region
     * @param string $postcode Postcode
     * @param string $country Country
     * @param string $tel Telephone
     *
     * @return string HTML Vcard
     */
    static public function vcard($givenname, $additionalname = '', $familyname = '', $url = '', $org = '', $email = '', $streetaddress = '', $locality = '', $region = '', $postcode = '', $country = '', $tel = '') {
        $html = '<div id="hcard-' . self::createCleanURL(trim($givenname . ' ' . $additionalname . ' ' . $familyname)) . '" class="vcard">';
        if ($url != '') {
            $html .= '<a class="url fn n" href="' . $url . '">';
        }

        $html .= '<span class="given-name">' . $givenname . '</span>';
        $html .= '<span class="additional-name">' . $additionalname . '</span>';
        $html .= '<span class="family-name">' . $familyname . '</span>';

        if ($url != '') {
            $html .= '</a>';
        }
        if ($org != '') {
            $html .= '<div class="org">' . $org . '</div>';
        }
        if ($email != '') {
            $html .= '<a class="email" href="mailto:' . $email . '">' . $email . '</a>';
        }

        $addr = false;
        if (($streetaddress != '') || ($locality != '') || ($region != '') || ($postcode != '') || ($country != '')) {
            $addr = true;
        }

        if ($addr) {
            $html .= '<div class="adr">';
            if ($streetaddress != '') {
                $html .= '<div class="street-address">' . $streetaddress . '</div>';
            }
            if ($locality != '') {
                $html .= '<span class="locality">' . $locality . '</span>, ';
            }
            if ($region != '') {
                $html .= '<span class="region">' . $region . ' </span> ';
            }
            if ($postcode != '') {
                $html .= '<span class="postal-code">' . $postcode . '</span>';
            }
            if ($country != '') {
                $html .= '<span class="country-name">' . $country . '</span>';
            }
            $html .= '</div>';
        }
        if ($tel != '') {
            $html .= '<div class="tel">' . $tel . '</div>';
        }
        $html .= '</div>';
        return $html;
    }

    /**
     * Allows you to display parented <option> elements.
     *
     * @param array  $ar        Input array (All items - multidimensional array)
     * @param int    $iSelected Currently active value
     * @param int    $parent    Current Parent (Defaults to 0 - for loop purposes)
     * @param string $idcol     Name of ID column
     * @param string $namecol   Name of >NAME< column
     * @param int    $stepCount For loop purposes
     *
     * @return string HTML <option>s
     */
    static public function showParentedOptions($ar, $iSelected, $parent = 0, $idcol = 'id', $namecol = 'name', $stepCount = 0) {
        $r = '';
        if (isset($ar[$parent])) {
            $prefix = '';
            if ($stepCount == 0) {
                for ($i = 0; $i <= $stepCount; $i++) {
                    $prefix .= ' - ';
                }
                $prefix .= ' > ';
            }
            foreach ($ar[$parent] as $id => $a) {
                $s = ($iSelected != $a[$idcol]) ? '' : ' selected="selected"';
                $r .= '<option value="' . $a[$idcol] . '"' . $s . '>' . $prefix . $a[$namecol] . '</option>';
                if (isset($ar[$a[$idcol]])) {
                    // STITCH CHILDREN
                    $r .= self::showParentedOptions($ar, $iSelected, $a[$idcol], $idcol, $namecol, ($stepCount + 1));
                }
            }
        }
        return $r;
    }

    /**
     * getImgProportions
     *
     * Get the proportions of an image
     *
     * @param int  $h                Current Height
     * @param int  $w                Current Width
     * @param int  $th               Target Height
     * @param int  $tw               Target Width
     * @param bool $bTargetIsMaximum Boolean - the target heights and width are
     * the maximum boundaries. Defaults to false
     */
    static public function getImgProportions($h, $w, $th, $tw, $bTargetIsMaximum = false) {
        $off_x = 0;
        $off_y = 0;
        if ($bTargetIsMaximum) {
            $sc = min($tw / $w, $th / $h);
        } else {
            $sc = max($tw / $w, $th / $h);
        }
        $oh = ceil($h * $sc);
        $ow = ceil($w * $sc);
        if ($oh > $th)
            $off_y = floor(($oh / 2) - ($th / 2)) * -1;
        if ($ow > $tw)
            $off_x = floor(($ow / 2) - ($tw / 2)) * -1;

        return array('height' => $oh, 'width' => $ow, 'off_x' => $off_x, 'off_y' => $off_y);
    }

}
