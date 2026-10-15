<?php
/**
 * Retire obsolete theme-managed Service field groups without deleting data.
 *
 * TheOct Fields displays only published groups. Marking two legacy Service
 * definitions as draft is reversible and retains ALL saved post meta.
 * For the shared Archive Page group, exclude only the Services archive ID.
 *
 * No other CPTs, user-created groups, or frontend templates are modified.
 *
 * @package TheOct
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Execute only for administrators after the plugin's own installers ran.
 */
function theoct_retire_old_service_groups() {
    if ( ! current_user_can( 'manage_options' )
        || ! defined( 'THEOCT_FIELDS_VERSION' )
        || ! class_exists( '\TheOct\Fields\Core\DefinitionIO' )
        || ! class_exists( '\TheOct\Fields\Core\FieldGroupRepository' )
        || ! function_exists( 'theoct_archive_editor_id' ) ) {
        return;
    }

    $modified = false;

    foreach ( array(
        'theoct_field_group_service',
        'theoct_field_group_services_single_sections',
    ) as $key ) {
        $id = \TheOct\Fields\Core\DefinitionIO::findByDefinitionKey( $key );
        if ( ! $id ) {
            continue;
        }
        $group = get_post( $id );
        if ( ! $group || 'theoct_field_group' !== $group->post_type || 'publish' !== $group->post_status ) {
            continue;
        }
        // Do not delete definitions, existing post data, or field values.
        $result = wp_update_post(
            array( 'ID' => $id, 'post_status' => 'draft' ),
            true
        );
        if ( ! is_wp_error( $result ) && $result ) {
            $modified = true;
        }
    }

    $service_id = theoct_archive_editor_id( 'service' );
    $shared_id  = \TheOct\Fields\Core\DefinitionIO::findByDefinitionKey(
        'theoct_field_group_archive_pages'
    );
    if ( $service_id && $shared_id ) {
        $group = get_post( $shared_id );
        if ( $group && 'theoct_field_group' === $group->post_type ) {
            $rules = get_post_meta( $shared_id, '_theoct_location_rule_groups', true );
            if ( ! is_array( $rules ) || ! $rules ) {
                $rules = array(
                    array(
                        array( 'param' => 'post_type', 'operator' => '==', 'value' => 'theoct_archive' ),
                    ),
                );
            }
            $changed = false;
            foreach ( $rules as &$rule_group ) {
                $items = is_array( $rule_group['rules'] ?? null )
                    ? $rule_group['rules'] : $rule_group;
                if ( ! is_array( $items ) ) {
                    continue;
                }
                $excluded = false;
                foreach ( $items as $rule ) {
                    if ( ( $rule['param'] ?? '' ) === 'post_id'
                        && ( $rule['operator'] ?? '' ) === '!='
                        && (string) ( $rule['value'] ?? '' ) === (string) $service_id ) {
                        $excluded = true;
                        break;
                    }
                }
                if ( ! $excluded ) {
                    $items[] = array(
                        'param' => 'post_id', 'operator' => '!=', 'value' => (string) $service_id,
                    );
                    if ( isset( $rule_group['rules'] ) ) {
                        $rule_group['rules'] = $items;
                    } else {
                        $rule_group = $items;
                    }
                    $changed = true;
                }
            }
            unset( $rule_group );

            if ( $changed ) {
                update_post_meta( $shared_id, '_theoct_location_rule_groups', $rules );
                $modified = true;
            }
        }
    }

    if ( $modified ) {
        \TheOct\Fields\Core\FieldGroupRepository::instance()->clearCache();
    }
}
add_action( 'admin_init', 'theoct_retire_old_service_groups', 70 );
