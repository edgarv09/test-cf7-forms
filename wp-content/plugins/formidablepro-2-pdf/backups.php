<?php

function fpropdf_backups_sort($a, $b) {
    if ($a['ts'] > $b['ts'])
        return -1;
    return 1;
}

function fpropdf_frm_match_xml_form($edit_query, $form) {
    if (isset($edit_query['created_at'])) {
        $edit_query['created_at'] = date('Y-m-d H:i:s', strtotime("now"));
    }
    return $edit_query;
}

function fpropdf_restore_backup($filename, $force_id = false, $duplicate = false) {
    if (!file_exists($filename)) {
        throw new Exception('File wasn\'t uploaded or couldn\'t be found.');
    }

    $currentFileData = json_decode(file_get_contents($filename), true);

    if (!$currentFileData) {
        throw new Exception('File contains some invalid data. The plugin wasn\'t able to read it.');
    }

    if (isset($currentFileData['xml']) && $currentFileData['xml']) {
        $tmp = tempnam(PROPDF_TEMP_DIR, 'fproPdfXml');

        try {
            if (!file_exists($tmp)) {
                throw new Exception("Tmp folder " . PROPDF_TEMP_DIR . " not exists or not writable");
            }
        } catch (Exception $e) {
            echo '<div class="error" style="margin-left: 0;"><p>' . $e->getMessage() . '</p></div>';
            die();
        }

        file_put_contents($tmp, base64_decode($currentFileData['xml']));
        $form_fields = array();

        if ($duplicate) {
            add_filter('frm_match_xml_form', 'fpropdf_frm_match_xml_form', 10, 2);
        }
        $result = $import = FrmXMLHelper::import_xml($tmp);
        if ($duplicate) {
            remove_filter('frm_match_xml_form', 'fpropdf_frm_match_xml_form', 10);
        }

        global $wpdb;
        $dom = new DOMDocument;
        $success = $dom->loadXML(file_get_contents($tmp));

        try {
            if (!$success) {
                throw new Exception("There was an error when reading this XML file");
            } elseif (!function_exists('simplexml_import_dom')) {
                throw new Exception("Your server is missing the simplexml_import_dom function");
            }
        } catch (Exception $e) {
            echo '<div class="error" style="margin-left: 0;"><p>' . $e->getMessage() . '</p></div>';
            die();
        }

        $xml = simplexml_import_dom($dom);

        $form_id = (string) $xml->form->id;
        $new_form = false;

        if ($duplicate) {
            if (isset($import['forms'][$form_id])) {
                $form_id = $import['forms'][$form_id];
                $new_form = FrmForm::getOne($form_id);
                //If new form sucessfully imported
                if ($new_form) {
                    //Iterate each form included in backup
                    foreach ($xml->form as $xml_form) {
                        $xml_form_id = (string) $xml_form->id;
                        if (isset($import['forms'][$xml_form_id])) {
                            $xml_form_id = $import['forms'][$xml_form_id];

                            //Iterate over each field in included form
                            foreach ($xml_form->field as $item) {
                                $field_options = FrmAppHelper::maybe_json_decode((string) $item->field_options);

                                //Get new fields if field connected to form
                                if (isset($field_options['form_select']) && $field_options['form_select']) {
                                    foreach ($xml->form as $sub_form_key => $sub_form) {
                                        if ((string) $sub_form->id == $field_options['form_select']) {
                                            foreach ($sub_form->field as $sub_field_key => $sub_field) {
                                                //Get new fields for current form
                                                $fields = FrmField::getAll(array('fi.form_id' => (int) $xml_form_id), 'id ASC');
                                                foreach ($fields as $key => $field) {
                                                    if (strpos($field->field_key, (string) $sub_field->field_key) === 0) {
                                                        $form_fields[] = array(
                                                            'old_id' => (int) $sub_field->id,
                                                            'old_key' => (string) $sub_field->field_key,
                                                            'new_id' => $field->id,
                                                            'new_key' => $field->field_key
                                                        );
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }

                                //Get new fields for current form
                                $fields = FrmField::getAll(array('fi.form_id' => (int) $xml_form_id), 'id ASC');
                                foreach ($fields as $key => $field) {
                                    if (strpos($field->field_key, (string) $item->field_key) === 0) {
                                        $form_fields[] = array(
                                            'old_id' => (int) $item->id,
                                            'old_key' => (string) $item->field_key,
                                            'new_id' => $field->id,
                                            'new_key' => $field->field_key
                                        );
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        foreach ($xml->form->field as $item) {
            $field = array(
                'field_id' => (int) $item->id,
                'field_key' => (string) $item->field_key,
                'form_id' => $form_id
            );

            $search = array_search($field['field_key'], array_column($form_fields, 'old_key'));
            if ($search !== false) {
                $field['field_id'] = $form_fields[$search]['new_id'];
                $field['field_key'] = $form_fields[$search]['new_key'];
            }

            $result = $wpdb->get_var($wpdb->prepare(
                            "SELECT field_key FROM " . FPROPDF_WPFXFIELDS . " WHERE field_id = %s AND form_id = %d", array($field['field_id'], $field['form_id'])
            ));

            if (!$result) {
                $wpdb->insert(FPROPDF_WPFXFIELDS, $field, array('%d', '%s', '%s'));
            }
        }

        FrmXMLHelper::parse_message($result, $message, $errors);
        if ($errors) {
            throw new Exception('There were some errors when importing Formidable Form. ' . print_r($errors, true));
        }
    }

    // unset($currentFileData['xml']);
    // unset($currentFileData['pdf']);

    $map = $currentFileData['data'];
    extract($map);

    global $wpdb;
    $form = $currentFileData['form']['form_key'];
    $exists = $wpdb->get_var("SELECT COUNT(*) AS c FROM " . FPROPDF_WPFXLAYOUTS . " WHERE ID = $ID");

    if ($duplicate) {
        $exists = 0;
    }

    $index = $dname;
    $data = @unserialize($data);
    $formats = @unserialize($formats);

    if ($currentFileData['salt'] != FPROPDF_SALT) {
        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . FPROPDF_WPFXLAYOUTS . " WHERE name = %s", $name), ARRAY_A);
        if ($row && !$duplicate) {
            $exists = true;
            $ID = $row['ID'];
        }
    }

    $lang = isset($lang) ? $lang : 0;

    if ($exists) {
        wpfx_updatelayout($ID, esc_sql($name), esc_sql($file), $visible, esc_sql($form), $index, $data, $formats, $add_att, $passwd, $lang, $add_att_ids, $default_format, $name_email, $restrict_role, $restrict_user);
    } else {

        if ($duplicate && $new_form) {

            $unique_id = time();

            $str_search = array();
            $str_replace = array();

            $form = $new_form->form_key;
            $add_att_ids = "";

            while (file_exists(FPROPDF_FORMS_DIR . '/' . $unique_id . '_' . $file)) {
                $unique_id = $unique_id + 1;
            }
            $file = $unique_id . '_' . $file;

            foreach ($data as $key => $field) {
                $search = array_search($field['0'], array_column($form_fields, 'old_key'));
                if ($search !== false) {
                    $data[$key]['0'] = $form_fields[$search]['new_key'];
                }
            }

            foreach ($form_fields as $form_field) {
                $str_search[] = "[" . $form_field['old_id'] . "]";
                $str_search[] = "[" . $form_field['old_id'] . ":label]";
                $str_search[] = "[" . $form_field['old_key'] . "]";
                $str_search[] = "[" . $form_field['old_key'] . ":label]";

                $str_replace[] = "[" . $form_field['new_id'] . "]";
                $str_replace[] = "[" . $form_field['new_id'] . ":label]";
                $str_replace[] = "[" . $form_field['new_key'] . "]";
                $str_replace[] = "[" . $form_field['new_key'] . ":label]";
            }

            foreach ($formats as $key => $format) {
                if (isset($format['2']) && $formats[$key]['2']) {
                    $formats[$key]['2'] = str_replace($str_search, $str_replace, $format['2']);
                }
            }

            if ($name_email) {
                $name_email = str_replace($str_search, $str_replace, $name_email);
            }

            $name = $unique_id . '_' . $name;
        }

        $r = wpfx_writelayout(esc_sql($name), esc_sql($file), $visible, esc_sql($form), $index, $data, $formats, $add_att, $passwd, $lang, $add_att_ids, $default_format, $name_email, $restrict_role, $restrict_user);

        if ($duplicate && $new_form) {

            $update = array();

            //Update Form name
            $update['name'] = $unique_id . '_' . $new_form->name;

            //Update Form Success Msg
            $form_options = FrmAppHelper::maybe_json_decode((string) $xml->form->options);
            if (isset($new_form->options['success_msg'])) {
                $success_msg = $new_form->options['success_msg'];

                //Replace Form Key & Form ID 
                $old_form_key = (string) $xml->form->form_key;
                $old_form_id = (string) $xml->form->id;
                $success_msg = preg_replace('/form=("|\'|)(' . $old_form_key . ')("|\'|\s)/i', 'form=${1}' . $new_form->form_key . '${3}', $success_msg);
                $success_msg = preg_replace('/form=("|\'|)(' . $old_form_id . ')("|\'|\s)/i', 'form=${1}' . $new_form->id . '${3}', $success_msg);

                //Replace Layout ID
                if (isset($ID) && $ID && $r) {
                    $old_layout_id = (int) $ID + 9;
                    $new_layout_id = (int) $r + 9;
                    $success_msg = preg_replace('/layout=("|\'|)(' . $old_layout_id . ')("|\'|\s)/i', 'layout=${1}' . $new_layout_id . '${3}', $success_msg);
                }

                $success_msg = str_replace($str_search, $str_replace, $success_msg);
                $update['options']['success_msg'] = $success_msg;
            }

            //By some reason default style not set
            $update['options']['custom_style'] = 1;

            FrmForm::update($new_form->id, $update);
        }
    }

    if (isset($currentFileData['pdf']) && $currentFileData['pdf']) {
        file_put_contents(FPROPDF_FORMS_DIR . '/' . $file, base64_decode($currentFileData['pdf']));
    }

    if ($force_id) {
        $wpdb->query($wpdb->prepare("UPDATE " . FPROPDF_WPFXLAYOUTS . " SET ID = $force_id WHERE name = %s", $name));
    }

    $exists = $wpdb->get_var("SELECT COUNT(*) AS c FROM " . FPROPDF_WPFXLAYOUTS . " WHERE ID = $ID");
}

function fpropdf_backups_page() {
    $files = array();

    if ($handle = opendir(FPROPDF_BACKUPS_DIR)) {

        while (false !== ($entry = readdir($handle))) {

            if (!preg_match('/\.json$/', $entry)) {
                continue;
            }

            $data = json_decode(file_get_contents(FPROPDF_BACKUPS_DIR . $entry), true);

            $files[] = array(
                'name' => $entry,
                'ts' => $data['ts'],
                'data' => $data,
            );
        }


        closedir($handle);
    }

    usort($files, 'fpropdf_backups_sort');

    if (isset($_GET['restore']) && $_GET['restore']) {
        foreach ($files as $currentFile) {
            if ($_GET['restore'] == $currentFile['name']) {
                try {
                    fpropdf_restore_backup(FPROPDF_BACKUPS_DIR . $currentFile['name']);
                } catch (Exception $e) {
                    die($e->getMessage());
                }
                set_transient('fpropdf_notification_restored', true, 1800);
                echo '<script>window.location.href = "?page=fpdf&tab=backups";</script>';
                exit;
            }
        }
    }

    if (isset($_GET['delete']) && $_GET['delete']) {
        foreach ($files as $currentFile) {
            if ($_GET['delete'] == $currentFile['name']) {
                @unlink(FPROPDF_BACKUPS_DIR . $currentFile['name']);
                set_transient('fpropdf_notification_deleted', true, 1800);
                echo '<script>window.location.href = "?page=fpdf&tab=backups";</script>';
                exit;
            }
        }
    }

    if (get_transient('fpropdf_notification_restored')) {
        echo '<div class="updated" style="margin-left: 0;"><p>Field map has been restored. You can now edit it in <a href="?page=fpdf">field map designer</a>.</p></div>';
        delete_transient('fpropdf_notification_restored');
    }

    if (get_transient('fpropdf_notification_deleted')) {
        echo '<div class="updated" style="margin-left: 0;"><p>Backup has been deleted.</p></div>';
        delete_transient('fpropdf_notification_deleted');
    }

    if (!count($files)) {
        echo '<div class="error" style="margin-left: 0;"><p>You don\'t have any backups yet. <br /> Backups will be automatically generated after you save or create a field map.</p></div>';
        return;
    }
    ?>

    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>Date</th>
                <th>Time</th>
                <th>Form</th>
                <th>Field Map</th>
                <th>Filename</th>
                <th>Number of fields</th>
                <th>&nbsp;</th>
            </tr>
        </thead>
        <tbody>

            <?php
            foreach ($files as $file):
                ?>

                <tr>
                    <td><?php echo date(get_option('date_format'), $file['data']['ts']); ?></td>
                    <td><?php echo date('H:i:s', $file['data']['ts']); ?></td>
                    <td><?php echo $file['data']['form']['name']; ?></td>
                    <td><?php echo $file['data']['data']['name']; ?></td>
                    <td><?php echo $file['data']['data']['file']; ?></td>
                    <td><?php echo @count(@unserialize($file['data']['data']['data'])); ?></td>
                    <td>
                        <p>
                            <a href="?page=fpdf&tab=backups&restore=<?php echo $file['name']; ?>" class="button button-primary" onclick="return confirm('Are you sure you want to restore this backup (<?php echo date(get_option('date_format') . ' H:i:s', $file['data']['ts']); ?>)?');">Restore</a>
                            <a href="?page=fpdf&tab=backups&delete=<?php echo $file['name']; ?>" class="button" onclick="return confirm('Are you sure you want to delete this backup (<?php echo date(get_option('date_format') . ' H:i:s', $file['data']['ts']); ?>)?');">Delete</a>
                        </p>
                        <p>

                            <a href="../wp-content/uploads/fpropdf-backups/<?php echo $file['name']; ?>" class="button" download="<?php echo esc_attr($file['data']['form']['name'] . " - " . $file['data']['data']['name'] . " - " . date("Y-m-d H-i-s", $file['data']['ts']) . ".json"); ?>">Download</a>
                        </p>
                    </td>
                </tr>

                <?php
            endforeach;
            ?>

        </tbody>
    </table>

    <?php
}
