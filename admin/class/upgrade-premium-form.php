<?php
// If this file is called directly, abort.
if (!defined('ABSPATH'))
    exit;
require_once ('header-footer.php');

function xmlsbw_search_posts_pages_callback()
{
    $results = array();
    $nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';
    
    if (!$nonce || !wp_verify_nonce($nonce, 'search_posts_nonce')) {
        $term = sanitize_text_field($_GET['term']);
        $posts = get_posts(
            array(
                'post_type' => 'any',
                'posts_per_page' => -1,
                's' => $term,
                'post_status' => 'publish',
                'fields' => 'ids',
            )
        );

        foreach ($posts as $post_id) {
            $post_title = get_the_title($post_id);
            $post_permalink = get_permalink($post_id);
            $results[] = array(
                'label' => $post_title,
                'permalink' => $post_permalink, 
            );
        }
    }
    wp_send_json($results);
}

function get_post_title_from_permalink() {
    $permalink = get_option('selected_page');
    $post_id = url_to_postid($permalink);
    $post_title = get_the_title($post_id);
    echo esc_html($post_title);
    exit;
}

function xmlsbw_upgrade_to_premium()
{
    ?>
    <div class="wrap-xmlsbw">
        <div class="inner-xmlsbw" id="inner-xmlsbw">
            <div class="left-box-xmlsbw xml-plans">
				<?php
					if(get_option('premium_access_allowed') == 1){
					?>
                		<h2 id="xml-heading" style="color: #4AB01A;"><?php esc_html_e('Pro Access Enabled', 'xml-sitemap-for-google'); ?></h2>
						<?php
					}else{
					?>
						<h2 id="xml-heading"><?php esc_html_e('Upgrade to Pro Features', 'xml-sitemap-for-google'); ?></h2> 
					<?php
					}	
				?>
				<div class="content" id="content" style="<?php echo (get_option('premium_access_allowed') == 1) ? 'border-top: 3px solid #4AB01A;' : 'border-top: 3px solid #FDB930;'; ?>">	
					<div class="content-inside1">
						<div class="xml-left-title">
							<p>
								<?php esc_html_e('Free Pro Features:', 'xml-sitemap-for-google'); ?>
							</p>
						</div>
						<div class="xml-right-des">
							<?php
							$premium_access_allowed = get_option('premium_access_allowed');
							$fill_color = $premium_access_allowed ? "#4AB01A" : "#FDBC33";
							?>
							<ul>
								<li>
									<svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 17 17" fill="none">
										<g clip-path="url(#clip0_1642_43)">
											<path d="M16.5871 7.49821L15.4277 6.33898C15.1525 6.06381 14.927 5.52001 14.927 5.13004V3.4906C14.927 2.71072 14.2899 2.07363 13.5102 2.07321H11.8701C11.4806 2.07321 10.9362 1.8473 10.661 1.57233L9.50173 0.413107C8.95092 -0.137702 8.04907 -0.137702 7.49826 0.413107L6.33903 1.57316C6.06361 1.84834 5.51857 2.07363 5.12988 2.07363H3.49044C2.7116 2.07363 2.07368 2.71072 2.07368 3.4906V5.13008C2.07368 5.51852 1.84822 6.06402 1.573 6.33903L0.413571 7.49825C-0.137653 8.04906 -0.137653 8.95092 0.413571 9.5026L1.573 10.6618C1.84839 10.937 2.07368 11.4823 2.07368 11.8708V13.5103C2.07368 14.2893 2.7116 14.9272 3.49044 14.9272H5.12992C5.51944 14.9272 6.06386 15.1527 6.33907 15.4277L7.4983 16.5873C8.04911 17.1377 8.95097 17.1377 9.50178 16.5873L10.661 15.4277C10.9364 15.1525 11.4806 14.9272 11.8702 14.9272H13.5103C14.2899 14.9272 14.927 14.2893 14.927 13.5103V11.8708C14.927 11.4806 15.1527 10.9368 15.4277 10.6618L16.5871 9.5026C17.1375 8.95092 17.1375 8.04902 16.5871 7.49821ZM7.37545 11.6877L4.24973 8.5616L5.25148 7.56005L7.3757 9.68426L11.7484 5.31261L12.7499 6.31416L7.37545 11.6877Z" fill="<?php echo $fill_color; ?>"/>
										</g>
										<defs>
											<clipPath id="clip0_1642_43">
												<rect width="17" height="17" fill="white"/>
											</clipPath>
										</defs>
									</svg>
									<span class="feture-item">Separate sitemap files</span>
								</li>
								<li>
									<svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 17 17" fill="none">
										<g clip-path="url(#clip0_1642_43)">
											<path d="M16.5871 7.49821L15.4277 6.33898C15.1525 6.06381 14.927 5.52001 14.927 5.13004V3.4906C14.927 2.71072 14.2899 2.07363 13.5102 2.07321H11.8701C11.4806 2.07321 10.9362 1.8473 10.661 1.57233L9.50173 0.413107C8.95092 -0.137702 8.04907 -0.137702 7.49826 0.413107L6.33903 1.57316C6.06361 1.84834 5.51857 2.07363 5.12988 2.07363H3.49044C2.7116 2.07363 2.07368 2.71072 2.07368 3.4906V5.13008C2.07368 5.51852 1.84822 6.06402 1.573 6.33903L0.413571 7.49825C-0.137653 8.04906 -0.137653 8.95092 0.413571 9.5026L1.573 10.6618C1.84839 10.937 2.07368 11.4823 2.07368 11.8708V13.5103C2.07368 14.2893 2.7116 14.9272 3.49044 14.9272H5.12992C5.51944 14.9272 6.06386 15.1527 6.33907 15.4277L7.4983 16.5873C8.04911 17.1377 8.95097 17.1377 9.50178 16.5873L10.661 15.4277C10.9364 15.1525 11.4806 14.9272 11.8702 14.9272H13.5103C14.2899 14.9272 14.927 14.2893 14.927 13.5103V11.8708C14.927 11.4806 15.1527 10.9368 15.4277 10.6618L16.5871 9.5026C17.1375 8.95092 17.1375 8.04902 16.5871 7.49821ZM7.37545 11.6877L4.24973 8.5616L5.25148 7.56005L7.3757 9.68426L11.7484 5.31261L12.7499 6.31416L7.37545 11.6877Z" fill="<?php echo $fill_color; ?>"/>
										</g>
										<defs>
											<clipPath id="clip0_1642_43">
												<rect width="17" height="17" fill="white"/>
											</clipPath>
										</defs>
									</svg>
									<span>HTML Sitemap</span>
								</li>
								<li>
									<svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 17 17" fill="none">
										<g clip-path="url(#clip0_1642_43)">
											<path d="M16.5871 7.49821L15.4277 6.33898C15.1525 6.06381 14.927 5.52001 14.927 5.13004V3.4906C14.927 2.71072 14.2899 2.07363 13.5102 2.07321H11.8701C11.4806 2.07321 10.9362 1.8473 10.661 1.57233L9.50173 0.413107C8.95092 -0.137702 8.04907 -0.137702 7.49826 0.413107L6.33903 1.57316C6.06361 1.84834 5.51857 2.07363 5.12988 2.07363H3.49044C2.7116 2.07363 2.07368 2.71072 2.07368 3.4906V5.13008C2.07368 5.51852 1.84822 6.06402 1.573 6.33903L0.413571 7.49825C-0.137653 8.04906 -0.137653 8.95092 0.413571 9.5026L1.573 10.6618C1.84839 10.937 2.07368 11.4823 2.07368 11.8708V13.5103C2.07368 14.2893 2.7116 14.9272 3.49044 14.9272H5.12992C5.51944 14.9272 6.06386 15.1527 6.33907 15.4277L7.4983 16.5873C8.04911 17.1377 8.95097 17.1377 9.50178 16.5873L10.661 15.4277C10.9364 15.1525 11.4806 14.9272 11.8702 14.9272H13.5103C14.2899 14.9272 14.927 14.2893 14.927 13.5103V11.8708C14.927 11.4806 15.1527 10.9368 15.4277 10.6618L16.5871 9.5026C17.1375 8.95092 17.1375 8.04902 16.5871 7.49821ZM7.37545 11.6877L4.24973 8.5616L5.25148 7.56005L7.3757 9.68426L11.7484 5.31261L12.7499 6.31416L7.37545 11.6877Z" fill="<?php echo $fill_color; ?>"/>
										</g>
										<defs>
											<clipPath id="clip0_1642_43">
												<rect width="17" height="17" fill="white"/>
											</clipPath>
										</defs>
									</svg>
									<span>Rename Sitemap URL</span>
								</li>
							</ul>
						</div>
					</div>
				</div>
                <form method="post" action="" id="xmlsbw-upgrade-to-premium" class="xml-plans-form">
					<div class="xml-pricing-cards" id="xml-pricing-cards">
						<div class="xml-pricing-card">
							<input type="radio" name="upgrade_option" class="upgrade_option" value="backlink" id="card1" checked <?php echo (get_option('upgrade_option') === 'backlink' || empty(get_option('upgrade_option'))) ? 'checked' : ''; ?>>
							<label for="card1" class="xml-plan-lable">FREE UPGRADE</label>
						</div>
						<div class="xml-pricing-card">
							<input type="radio" name="upgrade_option" class="upgrade_option" value="premium" id="card2" <?php checked(get_option('upgrade_option'), 'premium'); ?>>
							<label for="card2" class="xml-plan-lable">PAID UPGRADE</label>
						</div>
					</div>
                    <div class="free-table-data">    
						<div id="backlink" class="content" style="<?php echo (get_option('premium_access_allowed') == 1) ? 'border-top: 3px solid #4AB01A;' : 'border-top: 3px solid #FDB930;'; ?>">
							<p class="xml-plans-p border-bottom" id="xml-plans-p">
								<?php esc_html_e('We will be happy to provide you access to the premium features of this plugin for FREE if you can mention us on any of the pages in your website.
                                         To mention us, you can use any of the below mentioned Anchor text and link out to the given URL.', 'xml-sitemap-for-google'); ?>
							</p>
							<div class="content-inside border-bottom" id="content-inside">
								<div class="xml-left-title">
									<p>
										<?php esc_html_e('Keyword List:', 'xml-sitemap-for-google'); ?>
									</p>
								</div>
								<div class="xml-right-des">
									<p>
										<?php esc_html_e('Select one of the options from below dropdown. Copy html code shown below and paste in the page where you want to place the backlink.', 'xml-sitemap-for-google'); ?>
									</p>
									<?php
										$keyword_options = array(
											"custom software development" => "https://www.weblineindia.com/",
											"offshore software development company" => "https://www.weblineindia.com/about-us.html",
											"ai software development" => "https://www.weblineindia.com/ai-development.html",
											"software development outsourcing company" => "https://www.weblineindia.com/about-us.html",
											"software development outsourcing" => "https://www.weblineindia.com/",
											"offshore software development" => "https://www.weblineindia.com/",
											"software development services" => "https://www.weblineindia.com/",
											"hire software developers" => "https://www.weblineindia.com/hire-dedicated-developers.html",
											"hire software programmers" => "https://www.weblineindia.com/hire-dedicated-developers.html"
										);
										$saved_keyword = get_option('xmlsbw_saved_keyword');											
										preg_match('/<a\s+href="([^"]+)">([^<]+)<\/a>/', $saved_keyword, $matches);
										if (isset($matches[2])) {
											$saved_keyword = $matches[2];
										}
									?>

									<select id="xmlsbw_saved_keyword" name="xmlsbw_saved_keyword" class="xml-select-item">
										<?php foreach ($keyword_options as $text => $value):
											if(isset($saved_keyword) && !empty($saved_keyword)){
												if($saved_keyword == $text){
													$selected = 'selected=selected';
												}else{
													$selected = '';
												}
											}
											?>
											<option value="<?php echo esc_attr($value. '|' . $text); ?>" <?php echo $selected; ?>>
												<?php echo esc_html($text); ?>
											</option>
										<?php endforeach; ?>
									</select>
									<div class="link copy-link-box">
										<div name="dynamic-link" id="dynamic-link"></div>
										<?php
											$saved_keyword = get_option('xmlsbw_saved_keyword');
											if (preg_match('/>(.*?)</', $saved_keyword, $match)) {
												$value_between_tags = $match[1];
											}
										?>
										<input type="hidden" id="keyword_value" name="keyword_value" value="<?php echo esc_attr($value_between_tags); ?>">
										<div class="copy-btn" id="copy-button">
											<svg xmlns="http://www.w3.org/2000/svg" width="19" height="20" viewBox="0 0 19 20" fill="none">
											<path d="M3.4375 19H11.5625C12.9066 19 14 18.058 14 16.9V7.1C14 5.94199 12.9066 5 11.5625 5H3.4375C2.09338 5 1 5.94199 1 7.1V16.9C1 18.058 2.09338 19 3.4375 19ZM2.625 7.1C2.625 6.71411 2.9892 6.4 3.4375 6.4H11.5625C12.0108 6.4 12.375 6.71411 12.375 7.1V16.9C12.375 17.2859 12.0108 17.6 11.5625 17.6H3.4375C2.9892 17.6 2.625 17.2859 2.625 16.9V7.1Z" fill="#8D8D8D"/>
											<path d="M18.2483 12.586V4.02769C18.2483 2.61189 17.0966 1.46021 15.6808 1.46021H7.12249C6.64986 1.46021 6.26666 1.84341 6.26666 2.31603C6.26666 2.78866 6.64986 3.17186 7.12249 3.17186H15.6808C16.153 3.17186 16.5366 3.5559 16.5366 4.02769V12.586C16.5366 13.0586 16.9198 13.4418 17.3924 13.4418C17.8651 13.4418 18.2483 13.0586 18.2483 12.586Z" fill="#8D8D8D"/>
											</svg>
										</div>
									</div>
									<p>
									</p>
								</div>
							</div>
							<div class="content-inside border-bottom" id="select-page-div">
								<div class="xml-left-title">
									<p>
										<?php esc_html_e('Select the page:', 'xml-sitemap-for-google'); ?>
									</p>
								</div>
								<div class="xml-right-des">
									<input style="width: auto;" type="text" id="select_posts_input" name="select_posts_input" placeholder='Begin typing post title to search' value="">
									<input type="hidden" id="selected_post_permalink" name="selected_post_permalink" value="<?php echo esc_attr(get_option('selected_page')); ?>">
									<p class="description">
										<?php esc_html_e('Provide the names of the post/page on which you have mentioned us and get FREE access instantly.', 'xml-sitemap-for-google'); ?>
									</p>
								</div>       
							</div>
							<div class="content-inside" id="select-page-div">
								<div class="xml-left-title">
								</div>
								<div class="xml-right-des">
									<p class="text-val-before">
										<?php esc_html_e('Once done, please press validate button. Once validated your premium features will be enabled.', 'xml-sitemap-for-google'); ?>
									</p>
									<div class="validate-btn" id="validate-btn">
										<div id='validate-button' class="submit" style="display: flex; margin: 0; padding:0;">
											<input type="submit" name="save_upgrade_option" class="button button-primary" value="Validate"
											title="Updates your changes on click">
											<div class="loader">
												<div class="spinner"></div>
											</div>
										</div>
										<div class="success-message"></div>
									</div>
									<p class="text-val-after">
									<?php
										$contact_url = 'https://www.weblineindia.com/contact-us.html?utm_source=WP-Plugin&utm_medium=XML%20Sitemap%20For%20Google&utm_campaign=Free%20Support';
										printf(
											esc_html__('If you need help with Free Premium upgrade please feel free to %s', 'xml-sitemap-for-google'),
											sprintf(
												'<a href="%s" target="_blank" rel="noopener noreferrer">%s</a>',
												esc_url($contact_url),
												esc_html__('contact us', 'xml-sitemap-for-google')
											)
										);
									?>
									</p>
								</div>       
							</div>
							<div id="pop-up-box-upgrade" class="pop-up-box text-center" style="display: none;">
								<button class="close-popup">
									<svg viewPort="0 0 12 12" version="1.1"
										xmlns="
									http://www.w3.org/2000/svg">
									<line x1="1" y1="11" 
											x2="11" y2="1" 
											stroke="black" 
											stroke-width="2"/>
									<line x1="1" y1="1" 
											x2="11" y2="11" 
											stroke="black" 
											stroke-width="2"/>
									</svg>
								</button>
								<table class="pop-up-table">
									<tr>
										<td style="text-align:center;"><h3 style="color: #4AB01A;"><?php esc_html_e('Pro Access Enabled', 'xml-sitemap-for-google'); ?></h3></td>                                                
									</tr>
									<tr>
										<td class="description">
											<?php esc_html_e('We are happy to provide you access to the premium features of this plugin for FREE if you mention us on any of the pages on your website.', 'xml-sitemap-for-google'); ?>
										</td>
									</tr>
									<tr class="features-row">
										<td class="features">
											<svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 17 17" fill="none">
												<g clip-path="url(#clip0_1642_43)">
													<path d="M16.5871 7.49821L15.4277 6.33898C15.1525 6.06381 14.927 5.52001 14.927 5.13004V3.4906C14.927 2.71072 14.2899 2.07363 13.5102 2.07321H11.8701C11.4806 2.07321 10.9362 1.8473 10.661 1.57233L9.50173 0.413107C8.95092 -0.137702 8.04907 -0.137702 7.49826 0.413107L6.33903 1.57316C6.06361 1.84834 5.51857 2.07363 5.12988 2.07363H3.49044C2.7116 2.07363 2.07368 2.71072 2.07368 3.4906V5.13008C2.07368 5.51852 1.84822 6.06402 1.573 6.33903L0.413571 7.49825C-0.137653 8.04906 -0.137653 8.95092 0.413571 9.5026L1.573 10.6618C1.84839 10.937 2.07368 11.4823 2.07368 11.8708V13.5103C2.07368 14.2893 2.7116 14.9272 3.49044 14.9272H5.12992C5.51944 14.9272 6.06386 15.1527 6.33907 15.4277L7.4983 16.5873C8.04911 17.1377 8.95097 17.1377 9.50178 16.5873L10.661 15.4277C10.9364 15.1525 11.4806 14.9272 11.8702 14.9272H13.5103C14.2899 14.9272 14.927 14.2893 14.927 13.5103V11.8708C14.927 11.4806 15.1527 10.9368 15.4277 10.6618L16.5871 9.5026C17.1375 8.95092 17.1375 8.04902 16.5871 7.49821ZM7.37545 11.6877L4.24973 8.5616L5.25148 7.56005L7.3757 9.68426L11.7484 5.31261L12.7499 6.31416L7.37545 11.6877Z" fill="#4AB01A"></path>
												</g>
												<defs>
													<clipPath id="clip0_1642_43">
														<rect width="17" height="17" fill="white"></rect>
													</clipPath>
												</defs>
											</svg>
											<p>HTML Sitemap</p>
										</td>
										<td class="features">
											<svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 17 17" fill="none">
												<g clip-path="url(#clip0_1642_43)">
													<path d="M16.5871 7.49821L15.4277 6.33898C15.1525 6.06381 14.927 5.52001 14.927 5.13004V3.4906C14.927 2.71072 14.2899 2.07363 13.5102 2.07321H11.8701C11.4806 2.07321 10.9362 1.8473 10.661 1.57233L9.50173 0.413107C8.95092 -0.137702 8.04907 -0.137702 7.49826 0.413107L6.33903 1.57316C6.06361 1.84834 5.51857 2.07363 5.12988 2.07363H3.49044C2.7116 2.07363 2.07368 2.71072 2.07368 3.4906V5.13008C2.07368 5.51852 1.84822 6.06402 1.573 6.33903L0.413571 7.49825C-0.137653 8.04906 -0.137653 8.95092 0.413571 9.5026L1.573 10.6618C1.84839 10.937 2.07368 11.4823 2.07368 11.8708V13.5103C2.07368 14.2893 2.7116 14.9272 3.49044 14.9272H5.12992C5.51944 14.9272 6.06386 15.1527 6.33907 15.4277L7.4983 16.5873C8.04911 17.1377 8.95097 17.1377 9.50178 16.5873L10.661 15.4277C10.9364 15.1525 11.4806 14.9272 11.8702 14.9272H13.5103C14.2899 14.9272 14.927 14.2893 14.927 13.5103V11.8708C14.927 11.4806 15.1527 10.9368 15.4277 10.6618L16.5871 9.5026C17.1375 8.95092 17.1375 8.04902 16.5871 7.49821ZM7.37545 11.6877L4.24973 8.5616L5.25148 7.56005L7.3757 9.68426L11.7484 5.31261L12.7499 6.31416L7.37545 11.6877Z" fill="#4AB01A"></path>
												</g>
												<defs>
													<clipPath id="clip0_1642_43">
														<rect width="17" height="17" fill="white"></rect>
													</clipPath>
												</defs>
											</svg>
											<p>Compact Archives</p>
										</td>
										<td class="features">
											<svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 17 17" fill="none">
												<g clip-path="url(#clip0_1642_43)">
													<path d="M16.5871 7.49821L15.4277 6.33898C15.1525 6.06381 14.927 5.52001 14.927 5.13004V3.4906C14.927 2.71072 14.2899 2.07363 13.5102 2.07321H11.8701C11.4806 2.07321 10.9362 1.8473 10.661 1.57233L9.50173 0.413107C8.95092 -0.137702 8.04907 -0.137702 7.49826 0.413107L6.33903 1.57316C6.06361 1.84834 5.51857 2.07363 5.12988 2.07363H3.49044C2.7116 2.07363 2.07368 2.71072 2.07368 3.4906V5.13008C2.07368 5.51852 1.84822 6.06402 1.573 6.33903L0.413571 7.49825C-0.137653 8.04906 -0.137653 8.95092 0.413571 9.5026L1.573 10.6618C1.84839 10.937 2.07368 11.4823 2.07368 11.8708V13.5103C2.07368 14.2893 2.7116 14.9272 3.49044 14.9272H5.12992C5.51944 14.9272 6.06386 15.1527 6.33907 15.4277L7.4983 16.5873C8.04911 17.1377 8.95097 17.1377 9.50178 16.5873L10.661 15.4277C10.9364 15.1525 11.4806 14.9272 11.8702 14.9272H13.5103C14.2899 14.9272 14.927 14.2893 14.927 13.5103V11.8708C14.927 11.4806 15.1527 10.9368 15.4277 10.6618L16.5871 9.5026C17.1375 8.95092 17.1375 8.04902 16.5871 7.49821ZM7.37545 11.6877L4.24973 8.5616L5.25148 7.56005L7.3757 9.68426L11.7484 5.31261L12.7499 6.31416L7.37545 11.6877Z" fill="#4AB01A"></path>
												</g>
												<defs>
													<clipPath id="clip0_1642_43">
														<rect width="17" height="17" fill="white"></rect>
													</clipPath>
												</defs>
											</svg>
											<p>Exclude Posts/Pages</p>
										</td>
									</tr>
									<tr class="unlock-row">
										<td>
											<a href="<?php home_url() ?>/wp-admin/admin.php?page=sitemap-settings" class="unlock-featues">Get Started Now</a>
										</td>
									</tr>
								</table>
                            </div>
						</div>    
						<div id="premium" class="content" style="display: none;">
								<p><a href="https://www.weblineindia.com/contact-us.html?utm_source=WP-Plugin&utm_medium=XML%20Sitemap%20For%20Google&utm_campaign=Free%20Support" target="_blank">Click here</a> to leave us a message to know more about the pricing and getting access to our premium features.</p>
						</div>
					</div>
				</form> 
			</div>         
			<div class="right-box-xmlsbw" id="right-box-xmlsbw">
                <?php 
                    xmlsbw_general_section_callback();
                ?>
            </div>  
        </div>
    </div>
    <?php
    add_filter('admin_footer_text', 'xmlsbw_admin_footer');
}

function xmlsbw_validate_upgrade_option($parsedData) {
    $upgrade_option = isset($parsedData['upgrade_option']) ? $parsedData['upgrade_option'] : '';
    $selected_pages = isset($parsedData['selected_post_permalink']) ? $parsedData['selected_post_permalink'] : '';
    $xmlsbw_saved_keyword = isset($parsedData['xmlsbw_saved_keyword']) ? $parsedData['xmlsbw_saved_keyword'] : '';

    if ($upgrade_option === 'backlink' && $selected_pages) {		
       
		$parts = explode('|', $xmlsbw_saved_keyword);		
		$searchText = $parts[1];
		$searchHref = $parts[0];

		$pageContent = getPageContent($selected_pages);

		if (checkContent($pageContent, $searchText, $searchHref)) {
			return '1';
		} else {
			$xmlsbw_saved_keyword = isset($parsedData['xmlsbw_saved_keyword']) ? $parsedData['xmlsbw_saved_keyword'] : '';
			$parts = explode('|', $xmlsbw_saved_keyword);
			if(get_option( 'premium_access_allowed' ) != 0){
				update_option('premium_access_allowed', 0);
				get_json_response('Revoked');
			}
			update_option('upgrade_option', $parsedData['upgrade_option']);
			update_option('selected_page', $parsedData['selected_post_permalink']);
			update_option('selected_page_name', $parsedData['select_posts_input']);
			update_option('xmlsbw_saved_keyword', '<a href="' . $parts[0] . '">' . $parts[1] . '</a>');
			return 'The specified text and href are NOT present in the page source.';
		}	
    }
    else{
        return '0';
    }
}

// Function to fetch the content of a URL
function getPageContent($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}

function checkContent($content, $text, $href) {
	$isCommented = function($content, $position) {
		$before = strrpos(substr($content, 0, $position), '<!--');
		if ($before === false) {
			return false; 
		}
		$after = strpos($content, '-->', $before);
		if ($after === false || $after < $position) {
			return false; 
		}
		return true;
	};

    $textPosition = strpos($content, '>'.$text.'<');
    $hrefPosition1 = strpos($content, 'href="' . $href . '"');
    $hrefPosition2 = strpos($content, "href='" . $href . "'");

    $textFound = $textPosition !== false && !$isCommented($content, $textPosition);
    $hrefFound = ($hrefPosition1 !== false && !$isCommented($content, $hrefPosition1)) || ($hrefPosition2 !== false && !$isCommented($content, $hrefPosition2));

    return $textFound && $hrefFound;
}



function xmlsbw_save_upgrade_option() {
    if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'xmlsbw_save_upgrade_option_nonce')) {
        $formData = isset($_POST['formData']) ? $_POST['formData'] : '';
        $formData = filter_var($formData, FILTER_SANITIZE_STRING);
        parse_str($formData, $parsedData);

		$title = isset($_POST['title']) ? sanitize_text_field(wp_unslash($_POST['title'])) : '';

		$post_types = get_post_types(['public' => true], 'names');
		$args = array(
			'post_type' => $post_types,
			'post_status' => 'publish',
			'posts_per_page' => 1,
			'title' => $title
		);

		$query = new WP_Query($args);

		if ($query->have_posts()) {
			while ($query->have_posts()) {
				$query->the_post();
				$post = $query->post;
			}
			wp_reset_postdata();
		}

		if (!$post) {
			wp_send_json_error(array('valid' => false, 'message' => 'The selected post/page does not exist or is invalid.'));
			die();
		}

        $validation_result = xmlsbw_validate_upgrade_option($parsedData);
        if ($validation_result !== '1') {
			wp_send_json_error(array('valid' => true, 'message' => $validation_result));
            die();
        }

		$xmlsbw_saved_keyword = isset($parsedData['xmlsbw_saved_keyword']) ? $parsedData['xmlsbw_saved_keyword'] : '';
		$parts = explode('|', $xmlsbw_saved_keyword);

        update_option('upgrade_option', $parsedData['upgrade_option']);
        update_option('selected_page', $parsedData['selected_post_permalink']);
		update_option('selected_page_name', $parsedData['select_posts_input']);
		update_option('xmlsbw_saved_keyword', '<a href="' . $parts[0] . '">' . $parts[1] . '</a>');
		if(get_option( 'premium_access_allowed' ) != 1){
			update_option('premium_access_allowed', 1);
			get_json_response('Granted');
		}
			

        $response_data = array(
            'success' => true,
            'valid' => true,
            'message' => 'Settings Updated',
        );
        wp_send_json_success($response_data);
    } else {
        wp_send_json_error(array('message' => 'Nonce verification failed.'));
    }
    die();
}

?>