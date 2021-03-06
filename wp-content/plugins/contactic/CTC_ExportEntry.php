<?php
/*
    "Contact Form to Database" Copyright (C) 2011-2013 Michael Simpson  (email : michael.d.simpson@gmail.com)

    This file is part of Contactic.

    Contactic is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    Contactic is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Contactic.
    If not, see <http://www.gnu.org/licenses/>.
*/

require_once('CTC_ExportToHtmlTemplate.php');
require_once('CTC_Export.php');

class CTC_ExportEntry extends CTC_ExportToHtmlTemplate implements CTC_Export {

    var $submitTime;

    var $tableId = 'cfdb_entry';

    /**
     * @param $formName string
     * @param $options array of option_name => option_value
     * @return void
     */
    public function export($formName, $options = null) {

        if (!isset($options['submit_time'])) {
            return;
        }
        $this->submitTime = $options['submit_time'];
        unset($options['submit_time']);
        $options['content'] = 'TO BE REPLACED';
        $options['filter'] = 'submit_time=' . $this->submitTime;

        parent::export($formName, $options);
    }

    public function modifyContent($template) {
        /*$cssUrl = $this->plugin->getPluginDirUrl() . '/assets/css/misctable.css';
        $cssTag = '<link rel="stylesheet" href="' . $cssUrl . '">';

        $javascript = '';
        if ($this->plugin->isEditorActive()) {
            $cfdbEditUrl = $this->plugin->getAdminUrlPrefix('admin-ajax.php') . 'action=cfdb-edit';
            $cfdbGetValueUrl = $this->plugin->getAdminUrlPrefix('admin-ajax.php') . 'action=cfdb-getvalue';
            $loadImg = plugins_url('/../contact-form-to-database-extension-edit/img/load.gif', __FILE__);
            $javascript = sprintf(
                    '
<script type="text/javascript">
    jQuery(document).ready(
            function () {
                cfdbEntryEditable("%s", "%s", "%s", "%s");
            });
</script>',
                    $this->tableId, $cfdbEditUrl, $cfdbGetValueUrl, $loadImg);
        }*/

        //$template = "{{BEFORE}}$cssTag{{/BEFORE}}" ;
        $template = '<table id="' . $this->tableId . '" class="display table table-hover table-striped table-bordered dataTable" cellspacing="0" width="100%" role="grid" aria-describedby="contactic2_detail" style="width: 100%;"><tbody>';
        $cols = $this->dataIterator->getDisplayColumns();
        foreach ($cols as $aCol) {
            $colDisplayValue = $aCol; // Sanitize below
            if ($this->headers && isset($this->headers[$aCol])) {
                $colDisplayValue = $this->headers[$aCol];
            }
            
            $template .= sprintf('<tr><td><div>%s</div></td><td title="%s"><div id="%s,%s">${%s}</div></td></tr>',
                    esc_html($colDisplayValue),
                    esc_attr($aCol),
                    esc_attr($this->submitTime),
                    esc_attr($aCol),
                    // last $aCol is a template ${variable} string that gets replaced by the post-processing in
                    // CTCExportToHtmlTemplate, so don't sanitize here
                    $aCol);
        }

        $template .= sprintf('<tr><td><div>%s</div></td><td title="%s"><div id="%s,%s">${%s}</div></td></tr>',
                    esc_html('Source'),
                    esc_attr('Source'),
                    esc_attr($this->submitTime),
                    esc_attr('Source'),
                    // last $aCol is a template ${variable} string that gets replaced by the post-processing in
                    // CTCExportToHtmlTemplate, so don't sanitize here
                    '_ctc_referer');
        $template .= sprintf('<tr><td><div>%s</div></td><td title="%s"><div id="%s,%s">${%s}</div></td></tr>',
                    esc_html('Page'),
                    esc_attr('Page'),
                    esc_attr($this->submitTime),
                    esc_attr('Page'),
                    // last $aCol is a template ${variable} string that gets replaced by the post-processing in
                    // CTCExportToHtmlTemplate, so don't sanitize here
                    '_ctc_last_page_title');

        $template .= '</tbody></table>';
        //$template .= "{{AFTER}}$javascript{{/AFTER}}";
        return $template;
    }

}