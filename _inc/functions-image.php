<?php

    /**
     * Upload background image for post
     * @param $post_id
     * @return array|mixed - message with errors
     */
    function upload_image_for_post($post_id)
    {
    //////////////////////////////////CREATE DIRECTORY TO SAVE THE FILE TO (if needed) AND FILE NAME////////////////////

        $data = create_image_directory($post_id, $_FILES["image"]);
        if (isset($data['error']))
        {
            return $data;
        }
        extract($data); //$name, $extension, $message
    ////////////////////////////////////////SAVE THE FILE TO THE DIRECTORY/////////////////////////////////
        if ($name && move_uploaded_file($_FILES['image']['tmp_name'], $name)) {
            $message['error'] = false;
            $message['success'] = "The image ".basename($name)." was successfully uploaded.<br />";
        } else {
            $message['error'] = true;
            $message['save'] = "An error occurred while trying to save the file";
        }
    //////////////////////////STORE THE FILE INFO IN DB///////////////////////////////////////////////////////
        if (!$message['error'])
        {
            if (exists_image_for_post($post_id))
            {
                $update = update_image_in_DB($post_id, $name, $extension, $_FILES["image"]["size"]);
                if ($update['error'])
                {
                    return $update['message'];
                }
                return $message;

            }else
            {
                $add = add_image_to_DB($post_id, $name, $extension, $_FILES["image"]["size"]);
                if ($add['error'])
                {
                    return $add['message'];
                }
                return $message;
            }
        }
        return $message;
    }

    /**
     * Create directory for post background image (assets/img/{post_id})
     * @param $post_id
     * @param $file_info - uploaded file from user
     * @return array - name of the file, extension, size, message with error
     */
    function create_image_directory($post_id, $file_info)
    {
        $directory = IMAGE_PATH . "/$post_id/";
        $message['error'] = true;
        $name = '';

        if ($file_info["size"] > MAX_IMAGE_SIZE) {
            $message['size'] = "Image is too big. Maximum is " . number_format(MAX_IMAGE_SIZE / 1024 / 1024, 2) . "MB.<br />";
            return $message;
        }
        if (!getimagesize($file_info['tmp_name'])) {
            $message['image'] = "This is not an image.<br />";
            return $message;
        } else {
            $filename = basename($file_info['name']);
            $name = pathinfo($filename, PATHINFO_FILENAME);
            $name = iconv("utf-8", "us-ascii//TRANSLIT", $name);  //odstraníme pro jistotu diakritiku
            $name = strtolower($name);
            $name = preg_replace('~[^-a-z0-9_]+~', '', $name);  //po odstranění diakritiky zůstala transkripce ' takže ji také zrušíme
            $extension = pathinfo($filename, PATHINFO_EXTENSION);
            $extension = strtolower($extension);

            if (!file_exists($directory)) {
                mkdir($directory);
            }
            // We add a hyphen and number to the name; if it exists => we increase the number (picture-125).
            $increment = 1;
            while (file_exists($directory . $name . '-' . $increment . '.' . $extension)) {
                $increment++;
            }
            $name = $directory . $name . '-' . $increment . '.' . $extension;

            $message['error'] = false;
            return compact('name', 'extension', 'message', $name, $extension, $message);
        }
    }

    /**
     * Add info about image to DB
     * @param $post_id
     * @param $name
     * @param $extension
     * @param $size
     * @return mixed
     */
    function add_image_to_DB($post_id, $name, $extension, $size)
    {
        global $db;
        $state['error'] = true;
        $query = $db -> prepare("
                        INSERT INTO images (post_id, name, filename, mime, ext, size) 
                        VALUES (:post_id, :name, :filename, :mime, :ext, :size) 
                    ");
        $add = $query->execute([
            'post_id' => $post_id,
            'name' => pathinfo($name, PATHINFO_FILENAME),
            'filename' => basename($name),
            'mime' => mime_content_type($name),
            'ext' => $extension,
            'size' => $size
        ]);
        if (!$query->rowCount()) {
            $state['message'] = "Ooops, something went wrong with adding the image.";
            return $state;
        }
        $state['error'] = false;
        return $state;
    }

    /**
     * Update info about image in DB
     * @param $post_id
     * @param $name
     * @param $extension
     * @param $size
     * @return mixed
     */
    function update_image_in_DB($post_id, $name, $extension, $size)
    {
        global $db;
        $state['error'] = true;
        $query = $db -> prepare("
                        UPDATE images SET name = :name, filename = :filename, mime = :mime, ext = :ext, size = :size 
                        WHERE post_id = :post_id
                    ");
        $update = $query->execute([
            'post_id' => $post_id,
            'name' => pathinfo($name, PATHINFO_FILENAME),
            'filename' => basename($name),
            'mime' => mime_content_type($name),
            'ext' => $extension,
            'size' => $size
        ]);
        if (!$query->rowCount()) {
            $state['message'] = "Ooops, something went wrong with adding the image.";
            return $state;
        }
        $state['error'] = false;
        return $state;
    }

    /**
     * Is there any image for the post in DB?
     * @param $post_id
     * @return false or number of images for post in DB (1)
     */
    function exists_image_for_post($post_id)
    {
        global $db;
        $query = $db->prepare("
           SELECT COUNT(*) AS count FROM images WHERE post_id = :post_id 
        ");
        $query->execute(['post_id' => $post_id]);
        if ($query->rowCount() <> 0)
        {
            $count = $query->fetch(PDO::FETCH_OBJ);
            return $count->count;
        }
        return false;
    }

    /**
     * What's the name of the image for the post?
     * @param $post_id
     * @return false or name of the image for post
     */
    function get_image_name_for_post($post_id)
    {
    global $db;

    $query = $db->prepare("
            SELECT filename as name FROM images WHERE post_id = :post_id 
        ");
    $query->execute(['post_id' => $post_id]);

    if ($query->rowCount())
    {
        $image = $query->fetch(PDO::FETCH_OBJ);
        return $image->name;
    }
    return false;
    }

    /**
     * Delete image in DB
     * @param $post_id
     * @return bool
     */
    function delete_image_for_post($post_id)
    {
        global $db;
        $query = $db->prepare("
            DELETE FROM images WHERE post_id = :post_id
        ");
        $delete = $query->execute(['post_id' => $post_id]);
        if  (!$delete)
        {
            return false;
        }else
        {
            return true;
        }
    }

    /**
     * Delete directory with post images if the post is deleted
     * @param $dirPath
     */
    function deleteDir($dirPath) {
        if (! is_dir($dirPath)) {
            throw new InvalidArgumentException("$dirPath must be a directory");
        }
        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                deleteDir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dirPath);
    }