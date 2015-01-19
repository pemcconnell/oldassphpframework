<?php
/**
 * download.c.php
 *
 * Simple download class.
 *
 * @author Peter McConnell <pemcconnell@googlemail.com>
 */

class Download {
    
    /**
     * forceDl
     * 
     * Forces the browser to download the file. If the file does not exist it
     * triggers an error an creates a 404
     * 
     * @param string $path Path to file
     * 
     * @return null
     * 
     */
    public function forceDl($path) {
        if (!file_exsts($path)) {
            header("Content-type: application/force-download");
            header("Content-Transfer-Encoding: Binary");
            header("Content-length: " . filesize($path));
            header("Content-disposition: attachment; filename=\"" . basename($path) . "\"");
            readfile($path);
            die();
        } else { // 404
            global $CONSOLE;
            $CONSOLE -> error('Attempted to force download ' . $path);
            HttpError::virtualiseFrontendFourOhFour();
            die();
        }
    }

}
