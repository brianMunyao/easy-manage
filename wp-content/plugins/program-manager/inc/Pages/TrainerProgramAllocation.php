<?php

// /**
//  * @package ProgramManager
//  */

// namespace Inc\Pages;

// use WP_Error;

// class TrainerProgramAllocation
// {
//     public function register()
//     {
//         $this->create_trainer_program_allocation_table();
//         add_action('rest_api_init', [$this, 'register_routes']);
//     }

//     public function create_trainer_program_allocation_table()
//     {
//         global $wpdb;
//         $programs_table = $wpdb->prefix . 'programs';
//         $trainers_table = $wpdb->prefix . 'trainers';
//         $allocation_table = $wpdb->prefix . 'program_trainers_allocation';

//         $sql = "CREATE TABLE IF NOT EXISTS $allocation_table (
//                     program_allocation_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
//                     program_id INT NOT NULL,
//                     trainer_id INT NOT NULL
//                     -- FOREIGN KEY (program_id) REFERENCES $programs_table (program_id),
//                     -- FOREIGN KEY (trainer_id) REFERENCES $trainers_table (trainer_id)
//                 );";
//         require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
//         dbDelta($sql);
//     }

//     public function register_routes()
//     {
//     }

//     public function allocate_program($request)
//     {
//         $program_id = $request['program_id'];
//         $trainer_id = $request['trainer_id'];

//         global $wpdb;
//         $programs_table = $wpdb->prefix . 'programs';
//         $TPA_table = $wpdb->prefix . 'TPA'; // TrainerProgramAllocate

//         //TODO: Update this incase a program is completed
//         $current_allocations = $wpdb->get_results("SELECT * FROM $TPA_table JOIN $programs_table WHERE trainer_id=$trainer_id");

//         if (count($current_allocations) > 0) {
//             return new WP_Error(400, "Trainer already has an ongoing program");
//         }
//         $res = $wpdb->insert($TPA_table, [
//             'program_id' => $program_id,
//             'trainer_id' => $trainer_id
//         ]);

//         if (is_wp_error($res)) {
//             return new WP_Error(400, "Error allocating program", $res);
//         }
//         return $res;
//     }

//     public function create_trainee($request)
//     {
//         $result = wp_insert_user([
//             'user_login' => $request['email'],
//             'user_email' => $request['email'],
//             'user_pass' => $request['password'],
//             'role' => 'trainee',
//             'meta_input' => [
//                 'is_deactivated' => 0,
//                 'is_deleted' => 0,
//                 'fullname' => $request['fullname']
//             ]
//         ]);

//         if (is_wp_error($result)) {
//             return new WP_Error(400, 'Trainee Creation Failed', $result);
//         }
//         return $result;
//     }
// }
