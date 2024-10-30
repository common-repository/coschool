<?php
/**
 * All question related functions
 */
namespace Codexpert\CoSchool\App\Question;
use Codexpert\CoSchool\Helper;

/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @package Plugin
 * @subpackage Question
 * @author Codexpert <hi@codexpert.io>
 */
class Meta {

	/**
	 * Generates config metabox
	 * 
	 * @uses add_meta_box()
	 */
	public function config() {
		add_meta_box( 'coschool-question-config', __( 'Configuration', 'coschool' ), [ $this, 'callback_config' ], 'question', 'side', 'high' );
		add_meta_box( 'coschool-question-answers', __( 'Answers', 'coschool' ), [ $this, 'callback_answers' ], 'question', 'normal', 'high' );
	}

	public function callback_config() {
		echo Helper::get_view( 'config', 'views/metabox/question' );
	}

	public function callback_answers() {
		echo Helper::get_view( 'answers', 'views/metabox/question' );
	}
}