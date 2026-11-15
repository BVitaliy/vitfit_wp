<?php
/**
 * Block «Story: how kombucha is made» (markup: template-parts/home-story.php).
 *
 * A scroll scene (app-story.js): the bottle fills step by step — tea → SCOBY →
 * fermentation → bottling. The animation has exactly four stages, so the
 * block has four steps (ACF min = max = 4). The drawn bottle is part of the
 * scene and stays in the template; titles, texts and chips are editable.
 *
 * @package vitfit
 *
 * @var array $args Builder row.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$vtf_steps = array_slice( (array) ( $args['steps'] ?? [] ), 0, 4 );

if ( count( $vtf_steps ) < 4 ) {
	return;
}
?>
<section class="story js-story" id="<?php echo esc_attr( (string) ( $args['anchor'] ?? '' ) ?: 'kombucha' ); ?>">
	<div class="container story__head">
		<?php vtf_title( $args, 'h2', 'h2 story__title' ); ?>
		<?php if ( ! empty( $args['lead'] ) ) { ?>
			<p class="text-lg story__lead"><?php vtf_br( $args['lead'] ); ?></p>
		<?php } ?>
	</div>

	<div class="story__track">
		<div class="story__stage">
			<div class="story__visual">
				<span class="story__disc js-st-disc"></span>
				<span class="story__fizz" aria-hidden="true"><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i></span>
				<span class="story__sparks js-st-sparks" aria-hidden="true"><svg viewBox="0 0 24 24"><use href="#i-star" /></svg><svg viewBox="0 0 24 24"><use href="#i-star" /></svg><svg viewBox="0 0 24 24"><use href="#i-star" /></svg><svg viewBox="0 0 24 24"><use href="#i-star" /></svg></span>
				<div class="story__float">
				<svg class="story__bottle js-story-bottle" viewBox="-80 -110 360 690" aria-hidden="true">
					<defs>
						<clipPath id="st-clip"><path d="M68 52H132V76C132 100 190 110 190 150V436C190 455 177 468 158 468H42C23 468 10 455 10 436V150C10 110 68 100 68 76Z" /></clipPath>
						<!-- clear PET: light from the left, darker rim on the right -->
						<linearGradient id="st-glass" x1="0" x2="1">
							<stop offset="0" stop-color="#fff" stop-opacity=".55" />
							<stop offset=".1" stop-color="#fff" stop-opacity=".18" />
							<stop offset=".3" stop-color="#fff" stop-opacity=".06" />
							<stop offset=".7" stop-color="#fff" stop-opacity=".04" />
							<stop offset=".88" stop-color="#fff" stop-opacity=".22" />
							<stop offset="1" stop-color="#fff" stop-opacity=".5" />
						</linearGradient>
						<linearGradient id="st-rim" x1="0" x2="1">
							<stop offset="0" stop-color="#16110e" stop-opacity=".28" />
							<stop offset=".5" stop-color="#16110e" stop-opacity=".1" />
							<stop offset="1" stop-color="#16110e" stop-opacity=".32" />
						</linearGradient>
						<!-- liquid: lighter at the top, deep at the bottom (stops animated, app-story.js) -->
						<linearGradient id="st-liq" x1="0" x2="0" y1="0" y2="1">
							<stop class="js-st-liq1" offset="0" stop-color="#dfa64a" />
							<stop class="js-st-liq2" offset="1" stop-color="#7a4510" />
						</linearGradient>
						<linearGradient id="st-body-shade" x1="0" x2="1">
							<stop offset="0" stop-color="#000" stop-opacity=".38" />
							<stop offset=".12" stop-color="#000" stop-opacity=".05" />
							<stop offset=".22" stop-color="#fff" stop-opacity=".14" />
							<stop offset=".34" stop-color="#fff" stop-opacity="0" />
							<stop offset=".72" stop-color="#000" stop-opacity=".04" />
							<stop offset="1" stop-color="#000" stop-opacity=".42" />
						</linearGradient>
						<linearGradient id="st-streak" x1="0" x2="0" y1="0" y2="1">
							<stop offset="0" stop-color="#fff" stop-opacity="0" />
							<stop offset=".12" stop-color="#fff" stop-opacity=".75" />
							<stop offset=".8" stop-color="#fff" stop-opacity=".45" />
							<stop offset="1" stop-color="#fff" stop-opacity="0" />
						</linearGradient>
						<radialGradient id="st-bub" cx=".35" cy=".3" r=".75">
							<stop offset="0" stop-color="#fff" stop-opacity=".95" />
							<stop offset=".25" stop-color="#fff" stop-opacity=".35" />
							<stop offset=".7" stop-color="#fff" stop-opacity=".08" />
							<stop offset="1" stop-color="#fff" stop-opacity=".55" />
						</radialGradient>
						<radialGradient id="st-scoby" cx=".45" cy=".35" r=".7">
							<stop offset="0" stop-color="#fffaf0" />
							<stop offset=".6" stop-color="#f6dfb4" />
							<stop offset="1" stop-color="#e2b77a" />
						</radialGradient>
						<linearGradient id="st-leaf" x1="0" x2="1" y1="0" y2="1">
							<stop offset="0" stop-color="#7aa63c" />
							<stop offset="1" stop-color="#355e18" />
						</linearGradient>
						<linearGradient id="st-cap" x1="0" x2="1">
							<stop offset="0" stop-color="#3a322c" />
							<stop offset=".25" stop-color="#5a504a" />
							<stop offset=".45" stop-color="#1c1714" />
							<stop offset="1" stop-color="#0c0a09" />
						</linearGradient>
						<radialGradient id="st-floor" cx=".5" cy=".5" r=".5">
							<stop offset="0" stop-color="#16110e" stop-opacity=".35" />
							<stop offset="1" stop-color="#16110e" stop-opacity="0" />
						</radialGradient>
						<filter id="st-blur2" x="-20%" y="-20%" width="140%" height="140%"><feGaussianBlur stdDeviation="2" /></filter>
						<filter id="st-blur5" x="-40%" y="-40%" width="180%" height="180%"><feGaussianBlur stdDeviation="5" /></filter>
						<!-- light passing through the liquid: brighter core, darker sides -->
						<radialGradient id="st-core" cx=".42" cy=".55" r=".6">
							<stop offset="0" stop-color="#fff" stop-opacity=".28" />
							<stop offset=".6" stop-color="#fff" stop-opacity=".04" />
							<stop offset="1" stop-color="#000" stop-opacity=".12" />
						</radialGradient>
						<radialGradient id="st-caustic" cx=".5" cy=".5" r=".5">
							<stop offset="0" stop-color="#fff" stop-opacity=".7" />
							<stop offset="1" stop-color="#fff" stop-opacity="0" />
						</radialGradient>
						<radialGradient id="st-drop" cx=".35" cy=".3" r=".7">
							<stop offset="0" stop-color="#fff" stop-opacity=".95" />
							<stop offset=".35" stop-color="#fff" stop-opacity=".25" />
							<stop offset=".8" stop-color="#16110e" stop-opacity=".12" />
							<stop offset="1" stop-color="#fff" stop-opacity=".6" />
						</radialGradient>
						<linearGradient id="st-reflect" x1="0" x2="0" y1="0" y2="1">
							<stop offset="0" stop-color="#16110e" stop-opacity=".16" />
							<stop offset="1" stop-color="#16110e" stop-opacity="0" />
						</linearGradient>
					</defs>

					<!-- floor shadow -->
					<ellipse cx="100" cy="474" rx="104" ry="13" fill="url(#st-floor)" />
					<ellipse cx="100" cy="469" rx="70" ry="4" fill="#16110e" opacity=".28" filter="url(#st-blur2)" />
					<path d="M22 470H178C176 492 150 510 100 510 50 510 24 492 22 470Z" fill="url(#st-reflect)" />

					<!-- tea leaves falling in (step 1) -->
					<g class="js-st-leaves">
						<g transform="translate(78 -100)"><path d="M0 0C5-13 21-17 30-11 25 2 11 9 0 0Z" fill="url(#st-leaf)"/><path d="M1 -1C9-5 17-8 27-10" fill="none" stroke="#2c4d14" stroke-width="1.1" stroke-linecap="round"/></g>
						<g transform="translate(112 -80) rotate(40) scale(.85)"><path d="M0 0C5-13 21-17 30-11 25 2 11 9 0 0Z" fill="url(#st-leaf)"/><path d="M1 -1C9-5 17-8 27-10" fill="none" stroke="#2c4d14" stroke-width="1.1" stroke-linecap="round"/></g>
						<g transform="translate(58 -58) rotate(-30) scale(1.1)"><path d="M0 0C5-13 21-17 30-11 25 2 11 9 0 0Z" fill="url(#st-leaf)"/><path d="M1 -1C9-5 17-8 27-10" fill="none" stroke="#2c4d14" stroke-width="1.1" stroke-linecap="round"/></g>
						<g transform="translate(126 -36) rotate(80) scale(.8)"><path d="M0 0C5-13 21-17 30-11 25 2 11 9 0 0Z" fill="url(#st-leaf)"/><path d="M1 -1C9-5 17-8 27-10" fill="none" stroke="#2c4d14" stroke-width="1.1" stroke-linecap="round"/></g>
						<g transform="translate(90 -14) rotate(15)"><path d="M0 0C5-13 21-17 30-11 25 2 11 9 0 0Z" fill="url(#st-leaf)"/><path d="M1 -1C9-5 17-8 27-10" fill="none" stroke="#2c4d14" stroke-width="1.1" stroke-linecap="round"/></g>
					</g>

					<!-- the clear bottle -->
					<path d="M68 52H132V76C132 100 190 110 190 150V436C190 455 177 468 158 468H42C23 468 10 455 10 436V150C10 110 68 100 68 76Z" fill="url(#st-glass)" />

					<g clip-path="url(#st-clip)">
						<!-- liquid: the group slides up (y 470 = empty, 112 = full) -->
						<g class="js-st-liquid" transform="translate(0 470)">
							<rect class="js-st-liquid-fill" x="-10" y="0" width="220" height="480" fill="url(#st-liq)" />
							<rect x="-10" y="0" width="220" height="480" fill="url(#st-core)" />
							<ellipse cx="100" cy="340" rx="70" ry="22" fill="url(#st-caustic)" opacity=".5" filter="url(#st-blur5)" />
							<!-- yeast specks drifting in live kombucha -->
							<g class="story__specks" fill="#fff" fill-opacity=".5">
								<circle cx="89" cy="213" r="0.8" style="--d:-7.6s" />
								<circle cx="87" cy="56" r="1" style="--d:-1.0s" />
								<circle cx="144" cy="156" r="1.5" style="--d:-4.9s" />
								<circle cx="170" cy="157" r="0.8" style="--d:-6.6s" />
								<circle cx="128" cy="173" r="1" style="--d:-8.2s" />
								<circle cx="123" cy="111" r="0.8" style="--d:-1.2s" />
								<circle cx="137" cy="94" r="1" style="--d:-0.0s" />
								<circle cx="25" cy="137" r="1" style="--d:-8.6s" />
								<circle cx="66" cy="115" r="1.2" style="--d:-2.8s" />
								<circle cx="74" cy="306" r="1" style="--d:-1.6s" />
								<circle cx="74" cy="226" r="1.2" style="--d:-0.2s" />
								<circle cx="130" cy="114" r="1" style="--d:-2.4s" />
								<circle cx="108" cy="184" r="0.8" style="--d:-5.4s" />
								<circle cx="110" cy="63" r="1.2" style="--d:-3.2s" />
								<circle cx="102" cy="276" r="1.2" style="--d:-1.7s" />
								<circle cx="144" cy="120" r="0.8" style="--d:-2.3s" />
								<circle cx="29" cy="213" r="1.5" style="--d:-0.2s" />
								<circle cx="131" cy="217" r="1.5" style="--d:-5.2s" />
								<circle cx="26" cy="261" r="0.8" style="--d:-6.4s" />
								<circle cx="74" cy="90" r="1" style="--d:-8.4s" />
								<circle cx="142" cy="206" r="1.2" style="--d:-8.0s" />
								<circle cx="88" cy="266" r="0.8" style="--d:-5.3s" />
							</g>
							<!-- meniscus + moving surface light -->
							<ellipse cx="100" cy="1" rx="96" ry="6" fill="#fff" opacity=".22" />
							<path class="story__wave" d="M-80 0 Q -45 -6 -10 0 T 60 0 T 130 0 T 200 0 T 270 0 T 340 0 V10 H-80Z" fill="#fff" opacity=".22" />
							<!-- SCOBY: a living, uneven disc just under the surface (step 2) -->
							<g class="js-st-scoby" opacity="0">
								<!-- shadow it casts into the tea -->
								<path d="M20 14C40 30 160 30 180 14 170 34 30 34 20 14Z" fill="#7a4a14" opacity=".25" />
								<!-- the culture: a pale, uneven, gel-like disc -->
								<path d="M18 4C22-10 60-14 92-12 128-15 166-10 182 0 188 8 180 20 150 23 116 27 72 26 42 22 22 19 14 12 18 4Z" fill="url(#st-scoby)" stroke="#b97a35" stroke-opacity=".55" stroke-width="1.4" />
								<path d="M34 -2C58-8 86-6 108-8 132-9 154-6 170-1" fill="none" stroke="#fff" stroke-opacity=".75" stroke-width="2.4" stroke-linecap="round" />
								<path d="M30 12C58 16 96 18 140 15 156 14 166 12 172 9" fill="none" stroke="#c58a45" stroke-opacity=".5" stroke-width="1.4" />
								<ellipse cx="64" cy="6" rx="9" ry="3" fill="#fff" opacity=".35" />
								<ellipse cx="128" cy="4" rx="12" ry="3" fill="#fff" opacity=".3" />
								<circle cx="88" cy="10" r="2" fill="#b97a35" opacity=".45" /><circle cx="112" cy="14" r="1.6" fill="#b97a35" opacity=".45" /><circle cx="150" cy="9" r="1.4" fill="#b97a35" opacity=".45" /><circle cx="46" cy="11" r="1.3" fill="#b97a35" opacity=".45" />
							</g>
							<!-- bubbles: small spheres with a rim and a light (step 3) -->
							<g class="story__bubbles js-st-bubbles" fill="url(#st-bub)" stroke="#fff" stroke-opacity=".55" stroke-width=".8" opacity="0">
								<circle cx="40" cy="300" r="4" style="--d:0s;--t:3.2s" />
								<circle cx="66" cy="340" r="2.5" style="--d:-1.1s;--t:2.6s" />
								<circle cx="92" cy="320" r="5" style="--d:-2s;--t:3.6s" />
								<circle cx="118" cy="360" r="3" style="--d:-0.6s;--t:2.8s" />
								<circle cx="146" cy="310" r="4.5" style="--d:-1.7s;--t:3.3s" />
								<circle cx="168" cy="350" r="2.5" style="--d:-2.6s;--t:2.4s" />
								<circle cx="54" cy="380" r="3.5" style="--d:-3s;--t:3s" />
								<circle cx="132" cy="390" r="2" style="--d:-1.4s;--t:2.2s" />
								<circle cx="80" cy="400" r="3" style="--d:-2.3s;--t:2.9s" />
								<circle cx="156" cy="400" r="3.5" style="--d:-0.2s;--t:3.4s" />
								<circle cx="30" cy="420" r="5.5" style="--d:-1.9s;--t:4s" />
								<circle cx="104" cy="430" r="6" style="--d:-0.9s;--t:4.2s" />
								<circle cx="176" cy="430" r="4" style="--d:-2.8s;--t:3.7s" />
								<circle cx="70" cy="440" r="2" style="--d:-0.4s;--t:2.1s" />
							</g>
						</g>

						<!-- label wraps on (step 4) -->
						<g class="js-st-label" opacity="0">
							<rect x="0" y="182" width="200" height="228" fill="#ffd3de" />
							<rect x="22" y="196" width="108" height="66" rx="3" fill="#16110e" />
							<text x="29" y="226" fill="#fff" font-family="Ermilov, Arial Black, sans-serif" font-size="23.5" letter-spacing="-.6">KOMBU</text>
							<text x="29" y="253" fill="#fff" font-family="Ermilov, Arial Black, sans-serif" font-size="23.5" letter-spacing="-.6">CHA</text>
							<text x="136" y="212" fill="#16110e" font-family="Ermilov, Arial Black, sans-serif" font-size="9">ЧАЙНИЙ</text>
							<text x="136" y="223" fill="#16110e" font-family="Ermilov, Arial Black, sans-serif" font-size="9">ГРИБ</text>
							<rect x="22" y="268" width="156" height="22" rx="2" fill="#fff" />
							<text x="30" y="284" fill="#16110e" font-family="Ermilov, Arial Black, sans-serif" font-size="13">SWEET CHERRY</text>
							<text x="22" y="304" fill="#16110e" font-family="Arial, sans-serif" font-size="8" font-weight="700">live probiotic</text>
							<path d="M54 326 C58 316 66 312 76 311" fill="none" stroke="#16110e" stroke-width="1.6" />
							<circle cx="48" cy="344" r="14" fill="#f15f86" stroke="#16110e" stroke-width="1.6" />
							<circle cx="75" cy="338" r="12" fill="#f15f86" stroke="#16110e" stroke-width="1.6" />
							<path d="M118 404 C100 372 104 318 168 296 C182 348 160 392 118 404Z" fill="#63b648" />
							<path d="M118 404 C100 372 104 318 168 296 C140 330 126 366 118 404Z" fill="#f15f86" />
							<path d="M118 404 C100 372 104 318 168 296 C182 348 160 392 118 404Z M118 404 C132 360 146 330 168 296" fill="none" stroke="#16110e" stroke-width="1.8" />
							<rect x="22" y="356" width="44" height="44" rx="3" fill="#16110e" />
							<text x="44" y="374" fill="#fff" font-family="Ermilov, Arial Black, sans-serif" font-size="12" text-anchor="middle">VIT-</text>
							<text x="44" y="390" fill="#fff" font-family="Ermilov, Arial Black, sans-serif" font-size="12" text-anchor="middle">FIT</text>
							<!-- label edges catch a little shadow -->
							<rect x="0" y="182" width="200" height="3" fill="#000" opacity=".12" />
							<rect x="0" y="407" width="200" height="3" fill="#000" opacity=".12" />
						</g>

						<!-- PET ribs on the shoulder and near the base -->
						<g fill="none" stroke-linecap="round">
							<path d="M30 128Q100 140 170 128" stroke="#fff" stroke-opacity=".35" stroke-width="2" />
							<path d="M30 131Q100 143 170 131" stroke="#000" stroke-opacity=".07" stroke-width="1.5" />
							<path d="M18 146Q100 158 182 146" stroke="#fff" stroke-opacity=".3" stroke-width="2" />
							<path d="M18 149Q100 161 182 149" stroke="#000" stroke-opacity=".06" stroke-width="1.5" />
							<path d="M12 424Q100 434 188 424" stroke="#fff" stroke-opacity=".28" stroke-width="2" />
							<path d="M12 427Q100 437 188 427" stroke="#000" stroke-opacity=".07" stroke-width="1.5" />
						</g>
						<!-- cylinder shading -->
						<rect x="0" y="0" width="200" height="470" fill="url(#st-body-shade)" />
						<!-- glass thickness: a light inner contour -->
						<path d="M73 56H127V77C127 104 184 114 184 152V434C184 451 173 462 157 462H43C27 462 16 451 16 434V152C16 114 73 104 73 77Z" fill="none" stroke="#fff" stroke-opacity=".22" stroke-width="2" />
						<!-- soft reflections (blurred, like light on curved PET) -->
						<g filter="url(#st-blur2)">
							<rect x="22" y="150" width="12" height="270" rx="6" fill="url(#st-streak)" />
							<rect x="164" y="160" width="6" height="250" rx="3" fill="url(#st-streak)" opacity=".55" />
							<path d="M40 118C58 104 76 98 92 96" fill="none" stroke="#fff" stroke-opacity=".75" stroke-width="5" stroke-linecap="round" />
						</g>
						<rect x="27" y="170" width="3" height="200" rx="1.5" fill="#fff" opacity=".85" />
						<rect x="42" y="160" width="4" height="240" rx="2" fill="url(#st-streak)" opacity=".35" />
						<!-- petaloid PET base: five feet catching light -->
						<g opacity=".5">
							<path d="M16 440C24 458 36 464 50 464" fill="none" stroke="#fff" stroke-opacity=".55" stroke-width="2" />
							<path d="M62 466C70 456 82 456 90 466" fill="none" stroke="#000" stroke-opacity=".18" stroke-width="2" />
							<path d="M110 466C118 456 130 456 138 466" fill="none" stroke="#000" stroke-opacity=".18" stroke-width="2" />
							<path d="M184 440C176 458 164 464 150 464" fill="none" stroke="#fff" stroke-opacity=".35" stroke-width="2" />
						</g>
						<ellipse cx="100" cy="456" rx="74" ry="7" fill="#000" opacity=".14" />
						<!-- condensation when the bottle is chilled (step 4) -->
						<g class="js-st-drops" opacity="0" fill="url(#st-drop)">
							<ellipse cx="40" cy="140" rx="2.6" ry="3.2" />
							<ellipse cx="56" cy="176" rx="1.8" ry="2.2" />
							<ellipse cx="150" cy="150" rx="2.2" ry="2.8" />
							<ellipse cx="170" cy="200" rx="3" ry="3.8" />
							<ellipse cx="34" cy="240" rx="2" ry="2.5" />
							<ellipse cx="176" cy="280" rx="2.4" ry="3.0" />
							<ellipse cx="48" cy="420" rx="2.8" ry="3.5" />
							<ellipse cx="160" cy="430" rx="2" ry="2.5" />
							<ellipse cx="142" cy="180" rx="1.4" ry="1.8" />
							<ellipse cx="28" cy="330" rx="1.6" ry="2.0" />
							<ellipse cx="182" cy="360" rx="1.8" ry="2.2" />
							<ellipse cx="66" cy="440" rx="1.6" ry="2.0" />
							<ellipse cx="120" cy="440" rx="2.2" ry="2.8" />
							<ellipse cx="96" cy="132" rx="1.6" ry="2.0" />
							<ellipse cx="126" cy="124" rx="1.2" ry="1.5" />
						</g>
					</g>
					<!-- glass rim: the outline catches light on the left, shade on the right -->
					<path d="M68 52H132V76C132 100 190 110 190 150V436C190 455 177 468 158 468H42C23 468 10 455 10 436V150C10 110 68 100 68 76Z" fill="none" stroke="url(#st-rim)" stroke-width="1.6" />
					<path d="M72 54V76C72 92 60 100 44 112" fill="none" stroke="#fff" stroke-opacity=".7" stroke-width="2" stroke-linecap="round" />

					<!-- neck lip (the open bottle before the cap) -->
					<rect x="62" y="48" width="76" height="8" rx="3" fill="#fff" fill-opacity=".55" stroke="#16110e" stroke-opacity=".18" />

					<!-- cap screws on (step 4) -->
					<g class="js-st-cap" opacity="0">
						<rect x="61" y="46" width="78" height="8" rx="2" fill="#26201c" />
						<g stroke="#000" stroke-opacity=".5" stroke-width="1"><path d="M70 46V54M80 46V54M90 46V54M100 46V54M110 46V54M120 46V54M130 46V54" /></g>
						<rect x="63" y="2" width="74" height="46" rx="7" fill="url(#st-cap)" />
						<g stroke="#fff" stroke-opacity=".08" stroke-width="1.6"><path d="M70 7V44M76 7V44M82 7V44M88 7V44M94 7V44M100 7V44M106 7V44M112 7V44M118 7V44M124 7V44M130 7V44" /></g>
						<rect x="66" y="3" width="68" height="4" rx="2" fill="#fff" opacity=".14" />
						<rect x="72" y="8" width="7" height="36" rx="3.5" fill="#fff" opacity=".1" filter="url(#st-blur2)" />
					</g>
				</svg>
				</div>

				<span class="story__fact js-st-days"><b class="js-st-days-num">1</b> <span><?php echo wp_kses( __( 'day of<br>fermentation', 'vitfit' ), [ 'br' => [] ] ); ?></span></span>
			</div>


			<?php // clickable: scrolls to the step (app-story.js) ?>
			<ol class="story__progress">
				<?php foreach ( $vtf_steps as $vtf_n => $vtf_step ) { ?>
					<li<?php echo 0 === $vtf_n ? ' class="is-active"' : ''; ?>><button class="js-st-go" type="button" data-step="<?php echo esc_attr( (string) $vtf_n ); ?>"><span><?php echo esc_html( vtf_num( $vtf_n + 1 ) ); ?></span> <?php echo esc_html( (string) ( $vtf_step['title'] ?? '' ) ); ?></button></li>
				<?php } ?>
			</ol>
		</div>

		<div class="story__steps">
			<?php foreach ( $vtf_steps as $vtf_n => $vtf_step ) { ?>
				<?php $vtf_chips = array_filter( (array) ( $vtf_step['chips'] ?? [] ), static fn( $chip ) => ! empty( $chip['text'] ) ); ?>
				<article class="story__step<?php echo 0 === $vtf_n ? ' is-active' : ''; ?>">
					<span class="story__num"><?php echo esc_html( vtf_num( $vtf_n + 1 ) ); ?></span>
					<?php if ( ! empty( $vtf_step['title'] ) ) { ?>
						<h3 class="h3"><?php echo esc_html( $vtf_step['title'] ); ?></h3>
					<?php } ?>
					<?php if ( ! empty( $vtf_step['text'] ) ) { ?>
						<p class="text-lg"><?php vtf_br( $vtf_step['text'] ); ?></p>
					<?php } ?>
					<?php if ( ! empty( $vtf_chips ) ) { ?>
						<ul class="story__chips"><?php foreach ( $vtf_chips as $vtf_chip ) { ?><li><?php vtf_feature_icon( $vtf_chip['icon'] ?? '' ); ?><?php echo esc_html( $vtf_chip['text'] ); ?></li><?php } ?></ul>
					<?php } ?>
				</article>
			<?php } ?>
		</div>
	</div>
</section>
