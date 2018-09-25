<?php

/**
 *
 * @author Maksym Tsypliakov <maksym.tsypliakov@gmail.com>
 */

class PMAI_Admin_Import extends PMAI_Controller_Admin {

    /**
     * @param string $post_type
     * @param $post
     */
    public function index($post_type = 'post', $post ) {
        $this->data['post_type'] = $post_type;
        $this->data['post'] =& $post;
        $this->data['groups'] = array();
        $savedGroups = array();

        global $acf;

        if ($acf && version_compare($acf->settings['version'], '5.0.0') >= 0) {
            $savedGroups = get_posts(array(
                'posts_per_page' => -1,
                'post_type' => 'acf-field-group',
                'order' => 'ASC',
                'orderby' => 'title'
            ));
            $this->data['groups'] = acf_local()->groups;
        }
        else {
            $this->data['groups'] = apply_filters('acf/get_field_groups', array());
        }

        if (!empty($savedGroups)) {
            foreach ($savedGroups as $key => $group) {
                $groupData = acf_get_field_group($group);
                // Only render visible field groups.
                if (acf_get_field_group_visibility($groupData, array('post_type' => $post_type))) {
                    if (!isset($this->data['groups'][$group->post_name])) {
                        $this->data['groups'][] = array(
                            'ID' => $group->ID,
                            'title' => $group->post_title
                        );
                    }
                    else {
                        $this->data['groups'][$group->post_name]['ID'] = $group->ID;
                    }
                }
            }
        }

        if (!empty($this->data['groups'])) {
            foreach ($this->data['groups'] as $key => $group) {
                if (empty($this->data['groups'][$key]['ID']) && !empty($this->data['groups'][$key]['id'])) {
                    $this->data['groups'][$key]['ID'] = $this->data['groups'][$key]['id'];
                }
                elseif (empty($this->data['groups'][$key]['ID']) && !empty($this->data['groups'][$key]['key'])) {
                    $this->data['groups'][$key]['ID'] = $this->data['groups'][$key]['key'];
                }
            }
        }
        PMXI_Plugin::$session->set('acf_groups', $this->data['groups']);
        PMXI_Plugin::$session->save_data();
        $this->render();
	}			
}
