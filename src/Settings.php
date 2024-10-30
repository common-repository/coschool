<?php
/**
 * All settings related functions
 */
namespace Codexpert\CoSchool;
use Codexpert\Plugin\Base;
use Codexpert\Plugin\Settings as Settings_API;

/**
 * @package Plugin
 * @subpackage Settings
 * @author Codexpert <hi@codexpert.io>
 */
class Settings extends Base {

	public $plugin;

	public $slug;
	
	public $name;

	public $version;

	/**
	 * Constructor function
	 */
	public function __construct( $plugin ) {
		$this->plugin	= $plugin;
		$this->slug		= $this->plugin['TextDomain'];
		$this->name		= $this->plugin['Name'];
		$this->version	= $this->plugin['Version'];
	}
	
	public function init_menu() {
		$settings = [
			'id'            => $this->slug,
			'label'         => __( 'Settings', 'coschool' ),
			'title'         => "{$this->name} v{$this->version}",
			'header'        => __( 'Settings', 'coschool' ),
			'parent'     	=> 'coschool',
			'icon'			=> COSCHOOL_ASSET . '/img/icon.svg',
			'sections'      => [
				'coschool_general'	=> [
					'id'        => 'coschool_general',
					'label'     => __( 'General', 'coschool' ),
					'icon'      => 'dashicons-admin-tools',
					'sticky'	=> false,
					'fields'    => [
						'enroll_page' 	=> [
							'id'        => 'enroll_page',
							'label'     => __( 'Enrollment Page', 'coschool' ),
							'type'      => 'select',
							'desc'      => __( 'Set your enrollment page in case you\'re using native payment', 'coschool' ),
							'options'	=> Helper::get_posts( [ 'post_type' => 'page' ] ),
							'chosen'	=> true,
						],
						'dashboard_page' => [
							'id'        => 'dashboard_page',
							'label'     => __( 'Dashboard Page', 'coschool' ),
							'type'      => 'select',
							'desc'      => __( 'Set your dashboard page', 'coschool' ),
							'options'	=> Helper::get_posts( [ 'post_type' => 'page' ] ),
							'chosen'	=> true,
						],
					]
				],
				'coschool_payment'	=> [
					'id'        => 'coschool_payment',
					'label'     => __( 'Payment', 'coschool' ),
					'icon'      => 'dashicons-money-alt',
					'sticky'	=> false,
					'page_load'	=> true,
					'fields'    => [
						'currency' => [
							'id'        => 'currency',
							'label'     => __( 'Currency', 'coschool' ),
							'type'      => 'select',
							'desc'      => __( 'How would you like to take payment?', 'coschool' ),
							'options'	=> coschool_currencies( true ),
							'default'	=> 'USD',
							'chosen'	=> true,
						],
						'handler' => [
							'id'        => 'handler',
							'label'     => __( 'Handling Method', 'coschool' ),
							'type'      => 'select',
							'desc'      => __( 'How would you like to handle payment?', 'coschool' ),
							'options'	=> coschool_dependencies(),
							'chosen'	=> true,
						],
						'test_mode' => [
							'id'        => 'test_mode',
							'label'     => __( 'Test Mode', 'coschool' ),
							'type'      => 'switch',
							'desc'      => __( 'Enable test payment mode?', 'coschool' ),
							'condition'	=> [
								'key'	=> 'handler',
								'value'	=> 'native'
							]
						],
						'methods' => [
							'id'        => 'methods',
							'label'     => __( 'Payment Method', 'coschool' ),
							'type'      => 'select',
							'desc'      => __( 'What payment methods do you want to use?', 'coschool' ),
							'options'	=> coschool_payment_providers(),
							'default'	=> '[]',
							'multiple'	=> true,
							'chosen'	=> true,
							'condition'	=> [
								'key'	=> 'handler',
								'value'	=> 'native'
							]
						],
					]
				],
				'coschool_certificate'	=> [
					'id'        => 'coschool_certificate',
					'label'     => __( 'Certificate', 'coschool' ),
					'icon'      => 'dashicons-awards',
					'sticky'	=> false,
					'page_load'	=> true,
					'template'  => COSCHOOL_DIR . '/views/settings/certificate.php',
				],
				'coschool_email' => [
					'id'        => 'coschool_email',
					'label'     => __( 'Email', 'coschool' ),
					'icon'      => 'dashicons-email',
					'fields'    => [
						'image_heading'	=> [
							'id'		=> 'image_heading',
							'label'     => __( 'Branding', 'coschool' ),
							'type'      => 'divider',
						],
						'logo'			=> [
							'id'		=> 'logo',
							'label'     => __( 'Logo', 'coschool' ),
							'type'      => 'file',
							'default'	=> COSCHOOL_ASSET . '/img/logo.png',
						],
						'banner'		=> [
							'id'		=> 'banner',
							'label'     => __( 'Banner', 'coschool' ),
							'type'      => 'file',
							'default'	=> COSCHOOL_ASSET . '/img/email-banner.png',
						],
						'enroll_course_heading'	=> [
							'id'		=> 'enroll_course_heading',
							'label'     => __( 'Course Enrollment', 'coschool' ),
							'type'      => 'divider',
						],
						'enroll_enabled' => [
							'id'        => 'enroll_enabled',
							'label'     => __( 'Enable Enrollment Email', 'coschool' ),
							'desc'		=> __( 'Enable this if you want to send enroll email.', 'coschool' ),
							'type'      => 'switch',
						],
						'enroll_course_tabs' => [
							'id'      	=> 'enroll_course_tabs',
							'label'     => __( 'Sample Tabs' ),
							'type'      => 'tabs',
							'condition'	=> [
								'key'		=> 'enroll_enabled',
								'compare'	=> 'checked'
							],
							'items'     => [
								'enroll_course_instructor' => [
									'id'      	=> 'enroll_course_instructor',
									'label'     => __( 'Instructor', 'coschool' ),
									'fields'    => [
										'enroll_course_instructor_subject' => [
											'id'        => 'enroll_course_instructor_subject',
											'label'     => __( 'Subject', 'coschool' ),
											'type'      => 'text',
											'default'   => __( 'A new student just enrolled in your course!', 'coschool' ),
											'desc'      => __( 'Email that sends out to the student when they mark a lesson as complete.', 'coschool' ),
										],
										'enroll_course_instructor_body' => [
											'id'        => 'enroll_course_instructor_body',
											'label'     => __( 'Body', 'coschool' ),
											'type'      => 'wysiwyg',
											'default'   => __( 'Hi %%instructor_name%%! 

												%%student_name%% just enrolled in your course %%course_name%%. 
												We are really happy to see new students enrolling in your course, keep making valuable courses. 

												Happy teaching!
												', 'coschool' ),
											'desc'      => __( 'Email that sends out to the student when they mark a lesson as complete.', 'coschool' ),
										],
									],
								],
								'enroll_course_student' => [
									'id'      	=> 'enroll_course_student',
									'label'     => __( 'Student', 'coschool' ),
									'fields'    => [
										'enroll_course_student_subject' => [
											'id'        => 'enroll_course_student_subject',
											'label'     => __( 'Subject', 'coschool' ),
											'type'      => 'text',
											'default'   => __( 'Enrollment successful! 🤓', 'coschool' ),
											'desc'      => __( 'Email that sends out to the student when they mark a lesson as complete.', 'coschool' ),
										],
										'enroll_course_student_body' => [
											'id'        => 'enroll_course_student_body',
											'label'     => __( 'Body', 'coschool' ),
											'type'      => 'wysiwyg',
											'default'   => __( 'Hi %%student_name%%!

												Thank you for enrolling in the course %%course_name%%. We hope you will like it.

												Feel free to contact the course instructor - %%instructor_name%% if you have any questions regarding this course.

												Warmest Regards ❤️', 'coschool' ),
											'desc'      => __( 'Email that sends out to the student when they mark a lesson as complete.', 'coschool' ),
										],
									],
								],
							],
						],
						'application_submitted_heading'	=> [
							'id'		=> 'application_submitted_heading',
							'label'     => __( 'Application Submission', 'coschool' ),
							'type'      => 'divider',
						],
						'application_submission_enabled' => [
							'id'        => 'application_submission_enabled',
							'label'     => __( 'Enable Application Submission Email', 'coschool' ),
							'desc'		=> __( 'Enable this if you want to send application submission email.', 'coschool' ),
							'type'      => 'switch',
						],
						'application_submitted_tabs' => [
							'id'      	=> 'application_submitted_tabs',
							'label'     => __( 'Sample Tabs' ),
							'type'      => 'tabs',
							'condition'	=> [
								'key'		=> 'application_submission_enabled',
								'compare'	=> 'checked'
							],
							'items'     => [
								'application_submitted_instructor' => [
									'id'      	=> 'application_submitted_instructor',
									'label'     => __( 'Instructor', 'coschool' ),
									'fields'    => [
										'application_submitted_instructor_subject' => [
											'id'        => 'application_submitted_instructor_subject',
											'label'     => __( 'Subject', 'coschool' ),
											'type'      => 'text',
											'default'   => __( 'Application submitted successfully!', 'coschool' ),
											'desc'      => __( 'Email that sends out to the student when they mark a lesson as complete.', 'coschool' ),
										],
										'application_submitted_instructor_body' => [
											'id'        => 'application_submitted_instructor_body',
											'label'     => __( 'Body', 'coschool' ),
											'type'      => 'wysiwyg',
											'default'   => __( 'Hi %%instructor_name%%!


												Thank you for your interest to become an instructor on %%institution_name%%. 

												We have received your application. We will approve it after reviewing it within short notice.

												Wish to see you creating interactive courses soon!


												Best wishes!', 'coschool' ),
											'desc'      => __( 'Email that sends out to the student when they mark a lesson as complete.', 'coschool' ),
										],
									],
								],
								'application_submitted_admin' => [
									'id'      	=> 'application_submitted_admin',
									'label'     => __( 'Admin', 'coschool' ),
									'fields'    => [
										'application_submitted_admin_subject' => [
											'id'        => 'application_submitted_admin_subject',
											'label'     => __( 'Subject', 'coschool' ),
											'type'      => 'text',
											'default'   => __( 'Someone wants to become an instructor!', 'coschool' ),
											'desc'      => __( 'Email that sends out to the student when they mark a lesson as complete.', 'coschool' ),
										],
										'application_submitted_admin_body' => [
											'id'        => 'application_submitted_admin_body',
											'label'     => __( 'Body', 'coschool' ),
											'type'      => 'wysiwyg',
											'default'   => __( 'Hi %%admin_name%%!


												%%instructor_name%% wants to become an instructor on your platform / %%platform_name%%.

												Take your time to review and approve if it meets the standards.

												We love to see your platform grow with great instructors.


												Cheers!', 'coschool' ),
											'desc'      => __( 'Email that sends out to the student when they mark a lesson as complete.', 'coschool' ),
										],
									],
								],
							],
						],
						'application_approval_heading'	=> [
							'id'		=> 'application_approval_heading',
							'label'     => __( 'Application Approval', 'coschool' ),
							'type'      => 'divider',
						],
						'application_approval_enabled' => [
							'id'        => 'application_approval_enabled',
							'label'     => __( 'Enable Application Approval Email', 'coschool' ),
							'desc'		=> __( 'Enable this if you want to send application approval email.', 'coschool' ),
							'type'      => 'switch',
						],
						'application_approval_tabs' => [
							'id'      	=> 'application_approval_tabs',
							'label'     => __( 'Sample Tabs' ),
							'type'      => 'tabs',
							'condition'	=> [
								'key'		=> 'application_approval_enabled',
								'compare'	=> 'checked'
							],
							'items'     => [
								'application_approval_instructor' => [
									'id'      	=> 'application_approval_instructor',
									'label'     => __( 'Instructor', 'coschool' ),
									'fields'    => [
										'application_approval_instructor_subject' => [
											'id'        => 'application_approval_instructor_subject',
											'label'     => __( 'Subject', 'coschool' ),
											'type'      => 'text',
											'default'   => __( 'Welcome abroad!', 'coschool' ),
											'desc'      => __( 'Email that sends out to the student when they mark a lesson as complete.', 'coschool' ),
										],
										'application_approval_instructor_body' => [
											'id'        => 'application_approval_instructor_body',
											'label'     => __( 'Body', 'coschool' ),
											'type'      => 'wysiwyg',
											'default'   => __( 'Hi, %%instructor_name%%!

												Congratulation on becoming an Instructor! 🥳

												We have reviewed your application and are happy to approve it. 

												Let us know if you have any questions regarding our platform. We would love to walk you 
												through our system and policies.

												See you around 🤩', 'coschool' ),
											'desc'      => __( 'Email that sends out to the student when they mark a lesson as complete.', 'coschool' ),
										],
									],
								],
							],
						],
						'quiz_submitted_heading'	=> [
							'id'		=> 'quiz_submitted_heading',
							'label'     => __( 'Submitted Quiz', 'coschool' ),
							'type'      => 'divider',
						],
						'quiz_submitted_enabled' => [
							'id'        => 'quiz_submitted_enabled',
							'label'     => __( 'Enable Application Submitted Email', 'coschool' ),
							'desc'		=> __( 'Enable this if you want to send Quiz submitted email.', 'coschool' ),
							'type'      => 'switch',
						],
						'quiz_submitted_tabs' => [
							'id'      	=> 'quiz_submitted_tabs',
							'label'     => __( 'Sample Tabs' ),
							'type'      => 'tabs',
							'condition'	=> [
								'key'		=> 'quiz_submitted_enabled',
								'compare'	=> 'checked'
							],
							'items'     => [
								'quiz_submitted_instructor' => [
									'id'      => 'quiz_submitted_instructor',
									'label'     => __( 'Instructor', 'coschool' ),
									'fields'    => [
										'quiz_submitted_instructor_subject' => [
											'id'        => 'quiz_submitted_instructor_subject',
											'label'     => __( 'Subject', 'coschool' ),
											'type'      => 'text',
											'default'   => __( 'You have a new quiz submission.', 'coschool' ),
											'desc'      => __( 'Email that sends out to the student when they mark a lesson as complete.', 'coschool' ),
										],
										'quiz_submitted_instructor_body' => [
											'id'        => 'quiz_submitted_instructor_body',
											'label'     => __( 'Body', 'coschool' ),
											'type'      => 'wysiwyg',
											'default'   => __( 'Hi %%instructor_name%%,

												%%student_name%% just submitted a quiz. You can review it here %%quiz_review_url%% 

												%%student_name%% will be notified as soon as his submission is graded. This will be helpful to keep 
												a record and track their progress.
												', 'coschool' ),
											'desc'      => __( 'Email that sends out to the student when they mark a lesson as complete.', 'coschool' ),
										],
									],
								],
							],
						],
						'quiz_passed_heading'	=> [
							'id'		=> 'quiz_passed_heading',
							'label'     => __( 'Passed Quiz', 'coschool' ),
							'type'      => 'divider',
						],
						'quiz_passed_enabled' => [
							'id'        => 'quiz_passed_enabled',
							'label'     => __( 'Enable Quiz Passed Email', 'coschool' ),
							'desc'		=> __( 'Enable this if you want to send quiz passed email.', 'coschool' ),
							'type'      => 'switch',
						],
						'quiz_passed_tabs' => [
							'id'      	=> 'quiz_passed_tabs',
							'label'     => __( 'Sample Tabs' ),
							'type'      => 'tabs',
							'condition'	=> [
								'key'		=> 'quiz_passed_enabled',
								'compare'	=> 'checked'
							],
							'items'     => [
								'quiz_passed_student' => [
									'id'      => 'quiz_passed_student',
									'label'     => __( 'Student', 'coschool' ),
									'fields'    => [
										'quiz_passed_student_subject' => [
											'id'        => 'quiz_passed_student_subject',
											'label'     => __( 'Subject', 'coschool' ),
											'type'      => 'text',
											'default'   => __( 'You have passed! 🥳', 'coschool' ),
											'desc'      => __( 'Email that sends out to the student when they mark a lesson as complete.', 'coschool' ),
										],
										'quiz_passed_student_body' => [
											'id'        => 'quiz_passed_student_body',
											'label'     => __( 'Body', 'coschool' ),
											'type'      => 'wysiwyg',
											'default'   => __( 'Congratulations %%student_name%%!

												You have passed the quiz %%quiz_name%% and secured %%leaderboard_position%% place on the leaderboard. (include leaderboard position if possible) 

												Here’s your total score - (Include score if possible)

												We are eager to see you do more progress and rank higher on the leaderboard. 😎

												Keep it up! 
												', 'coschool' ),
											'desc'      => __( 'Email that sends out to the student when they mark a lesson as complete.', 'coschool' ),
										],
									],
								],
							],
						],
						'assignment_heading'	=> [
							'id'		=> 'assignment_heading',
							'label'     => __( 'Assignment Submission', 'coschool' ),
							'type'      => 'divider',
						],
						'assignment_submission_enabled' => [
							'id'        => 'assignment_submission_enabled',
							'label'     => __( 'Enable Assignment Submission Email', 'coschool' ),
							'desc'		=> __( 'Enable this if you want to send assignment submission email.', 'coschool' ),
							'type'      => 'switch',
						],
						'assignment_tabs' => [
							'id'      	=> 'assignment_tabs',
							'label'     => __( 'Sample Tabs' ),
							'type'      => 'tabs',
							'condition'	=> [
										'key'		=> 'assignment_submission_enabled',
										'compare'	=> 'checked'
							],
							'items'     => [
								'assignment_instructor' => [
									'id'      => 'assignment_instructor',
									'label'     => __( 'Instructor', 'coschool' ),
									'fields'    => [
										'assignment_instructor_subject' => [
											'id'        => 'assignment_instructor_subject',
											'label'     => __( 'Subject', 'coschool' ),
											'type'      => 'text',
											'default'   => __( 'Someone just submitted an assignment!', 'coschool' ),
											'desc'      => __( 'Email that sends out to the student when they mark a lesson as complete.', 'coschool' ),
										],
										'assignment_instructor_body' => [
											'id'        => 'assignment_instructor_body',
											'label'     => __( 'Body', 'coschool' ),
											'type'      => 'wysiwyg',
											'default'   => __( 'Hi %%instructor_name%%,

												You have an assignment submission from %%student_name%%. 

												You can review it on your dashboard or directly on this link - %%assignment_url%%.
												', 'coschool' ),
											'desc'      => __( 'Email that sends out to the student when they mark a lesson as complete.', 'coschool' ),
										],
									],
								],
							],
							'lesson_completed_subject' => [
								'id'        => 'lesson_completed_subject',
								'label'     => __( 'Lesson Completed', 'coschool' ),
								'type'      => 'text',
								'desc'      => __( 'Email that sends out to the student when they mark a lesson as complete.', 'coschool' ),
							],
							'lesson_completed_body' => [
								'id'        => 'lesson_completed_body',
								'label'     => __( 'Lesson Completed', 'coschool' ),
								'type'      => 'wysiwyg',
								'desc'      => __( 'Email that sends out to the student when they mark a lesson as complete.', 'coschool' ),
							],
						]
					],
				],

			]
		];

		// payment method config fields
		if( count( $methods = coschool_payment_methods() ) > 0 ) {

			$tabs = [];
			foreach ( $methods as $method ) {
				if( count( $fields = apply_filters( "coschool_payment_{$method}_config", [
					"{$method}_label"	=> [
						'id'		=> "{$method}_label",
						'label'		=> __( 'Label', 'coschool' ),
						'type'		=> 'text',
						'default'	=> coschool_payment_providers( $method ),
						'desc'		=> __( 'Label to show in the checkout section', 'coschool' ),
						'required'	=> true,
					],
					"{$method}_desc"	=> [
						'id'		=> "{$method}_desc",
						'label'		=> __( 'Description', 'coschool' ),
						'type'		=> 'text',
						'default'	=> sprintf( __( 'Pay with %s', 'coschool' ), coschool_payment_providers( $method ) ),
						'desc'		=> __( 'Description to show below the payment method name', 'coschool' ),
					],
					"{$method}_button"	=> [
						'id'		=> "{$method}_button",
						'label'		=> __( 'Button Text', 'coschool' ),
						'type'		=> 'text',
						'default'	=> __( 'Pay', 'coschool' ),
						'desc'		=> __( 'Payment button text for this payment method', 'coschool' ),
					],
				] ) ) ) {
					$tabs[ $method ] = [
						'id'		=> $method,
						'label'		=> coschool_payment_providers( $method ),
						'fields'	=> $fields,
					];
				}
			}

			if( count( $tabs ) > 0 ) {
				$settings['sections']['coschool_payment']['fields']['method_config'] = [
					'id'		=> 'method_config',
					'label'		=> __( 'Config', 'coschool' ),
					'type'		=> 'tabs',
					'items'		=> $tabs,
					'condition'	=> [
						'key'	=> 'handler',
						'value'	=> 'native'
					]
				];
			}
		}

		$settings = apply_filters( 'coschool_settings', $settings );

		new Settings_API( $settings );
	}

	/**
	 * Show certificate builder in the settings screen
	 */
	public function builder( $field, $section ) {
		if( $section['id'] != 'coschool_certificate' || $field['id'] != 'template' ) return;

		if( '' != $_certificate_html = get_option( '_certificate_html' ) ) {
			echo stripslashes( $_certificate_html );
		}

		printf( '<a href="%1$s" class="%2$s" id="%3$s">%4$s</a>', add_query_arg( [
			'page'			=> 'certificate-builder',
			'certificate'	=> 'default',
		], admin_url( 'admin.php' ) ), 'button button-primary button-hero', 'coschool-certificate-builder', __( 'Builder', 'coschool' ) );
	}

	/**
	 * Remove stored certificate when option is reset
	 */
	public function clear_certificate( $section ) {

		if( $section != 'coschool_certificate' ) return;

		delete_option( '_certificate_html' );
	}
}