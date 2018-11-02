<?php 
 	/*
    Plugin Name: jack 
    Description: Plugin to show Prêt à vivre form
    Author: Objectif 8
    Version: 1.0.1
    Author URI: http://www.objectif8.com
	*/
	defined( 'ABSPATH' ) or die( 'You do not belong here' );
	define ( 'JACK_THEME_VERSION', '1.0.1');

	register_activation_hook(__FILE__, 'my_plugin_activation');

	function my_plugin_activation() {
		update_option( 'jack_plugin_version', JACK_THEME_VERSION );

		return JACK_THEME_VERSION;
	}

    function my_plugin_is_current_version(){
        $version = get_option( 'jack_plugin_version' );
        return version_compare($version, JACK_THEME_VERSION, '=') ? true : false;
	}
	
	if ( !my_plugin_is_current_version() ) my_plugin_activation();

	add_action( 'wp_enqueue_scripts', 'jackScripts' );
	add_shortcode('jack_form', 'showForm');

	function jackScripts() {
		wp_register_style( 'jack-style', plugins_url('style.css', __FILE__) );
		wp_enqueue_style( 'jack-style' );

		wp_register_script( 'jack-script', plugins_url('javascript.js', __FILE__) );
		wp_enqueue_script( 'jack-script' );
	}

	function showForm($attributes = [], $content = null, $tag = '') {
		$attributes = array_change_key_case((array)$attributes, CASE_LOWER);
		$result = "";
		
		if (!empty($attributes["email"])) {
			$isFrench = empty($attributes["lang"]) ? true : $attributes["lang"] == "fr";

			$yes = $isFrench ? "Oui" : "Yes";
			$no = $isFrench ? "Non" : "No";
			$next = $isFrench ? "Suivant" : "Next";
			$prev = $isFrench ? "Retour" : "Back";
			$send = $isFrench ? "Envoyer" : "Send";

			$title = $isFrench ? "Créer mon" : "Create my";
			$email = $attributes["email"];

			$volets = [
				[ // 1
					"intro" => $isFrench ? "Afin de bien cibler vos besoins et de vous offrir un service de qualité, nous vous invitons à répondre à ce court formulaire."
										: "In order to target your needs and offer quality service, we suggest you fill this short form.",
					"questions" => [
						[
							"question" => $isFrench ? "Planifiez-vous acheter un condo?" : "Do you plan on buying a condo?",
							"answers" => [["label" => $yes], ["label" => $no]]
						]
					],
					"send" => false
				],
				[ // 2
					"questions" => [
						[
							"question" => $isFrench ? "Dans combien de temps comptez vous y habiter?" : "When do you intend to live there?",
							"answers" => [
								["label" => $isFrench ? "Moins de 3 mois" : "In less than 3 months"],
								["label" => $isFrench ? "3 à 6 mois" : "In 3 to 6 months"],
								["label" => $isFrench ? "6 à 18 mois" : "In 6 to 18 months"],
								["label" => $isFrench ? "Plus de 18 mois" : "In more than 18 months"]
							]
						]
					],
					"coordo" => $isFrench ? "Afin de vous créer une planche d'intentions et une proposition, nous vous expédierons un questionnaire plus détaillé."
											: "In order to create a board of intentions and a proposition for you, we will send you a more detailed survey."
				],
				[ // 3
					"questions" => [
						[
							"question" => $isFrench ? "Vivez-vous dans un autre type d'habitation?" : "Do you live in another type of housing?",
							"answers" => [["label" => $yes], ["label" => $no]]
						]
					],
					"send" => false
				],
				[ // 4
					"questions" => [
						[
							"question" => $isFrench ? "Quel type de résidence?" : "What type of housing?",
							"answers" => [
								["label" => $isFrench ? "Maison unifamiliale" : "Single family home"],
								["label" => $isFrench ? "Maison en copropriété" : "Coproperty house"],
								["label" => "Loft / Studio"],
								["label" => "Plex"]
							]
						]
					],
					"coordo" => $isFrench ? "Afin de répondre à vos besoin un designer communiquera avec vous."
											: "In order to meet your needs, a designer will comunicate with you."
				],
				[ // 5
					"questions" => [
						[
							"question" => $isFrench ? "Avez-vous un besoin où notre équipe de designers peut vous aider?"
													: "Do you any need with which our team of designers can help?",
							"answers" => [["label" => $yes], ["label" => $no]]
						]
					],
					"send" => false
				],
				[ // 6
					"coordo" => $isFrench ? "Afin de répondre à vos besoin un designer communiquera avec vous."
											: "In order to meet your needs, a designer will comunicate with you."
				],
				[ // 7
					"questions" => [
						[
							"question" => $isFrench ? "Demande en traitement..." : "Treating request..."
						]
					],
					"send" => true
				]
			];

			$result = <<<HTML
			<div id="volet-container" data-email="$email">
				<h2>$title Prêt à vivre !</h2>
HTML;

			$compteurVolet = 0;

			foreach ($volets as $volet) {
				$compteurVolet++;
				$sup = $compteurVolet == 1 ? " volet-visible" : "";
				$result .= <<<HTML
				<div id="volet-$compteurVolet" class="volet$sup">
HTML;

				if (!empty($volet["intro"])) {
					$intro = $volet["intro"];
					$result .= <<<HTML
					<p>$intro</p>
HTML;
				}

				if (!empty($volet["questions"])) {
					$compeurQuestion = 0;

					foreach ($volet["questions"] as $question) {
						$compeurQuestion++;
						$text = $question["question"];
						$result .= <<<HTML
					<h3>$text</h3>
HTML;

						if (!empty($question["answers"])) {
							$compteurAnswer = 0;
							$result .= <<<HTML
					<div class="same-line">
HTML;

							foreach ($question["answers"] as $answer) {
								$compteurAnswer++;
								$name = "v" . $compteurVolet . "q" . $compeurQuestion;
								$nameExt = $name . "r" . $compteurAnswer;
								$texte = $answer["label"];
						$result .= <<<HTML
						<input type="radio" name="$name" id="$nameExt" onclick="putParentBackgroundBack(this)">
						<label for="$nameExt">$texte</label>
HTML;
							}

							$result .= <<<HTML
					</div>
HTML;
						}
					}
				}

				if (!empty($volet["coordo"])) {
					$oblig = $isFrench ? "Obligatoire" : "Obligatory";
					$texte = $volet["coordo"];
					$whatCoordo = $isFrench ? "Quelles sont vos coordonnées?" : "What are your contact informations?";
					$placeholder = $isFrench ? "Votre réponse" : "Your answer";
					$lastName = $isFrench ? "Nom" : "Last name";
					$firstName = $isFrench ? "Prénom" : "First name";
					$email = $isFrench ? "Adresse courriel" : "Email address";
					$phone = $isFrench ? "Téléphone" : "Phone number";
					$city = $isFrench ? "Ville" : "City";
					$sub = $isFrench ? "Abonnement infolettre" : "Subscribe to newsletter";
					$subAnswer1 = $isFrench ? "Oui, je veux m'abonner et connaître les dernières tendances déco!" : "Yes, I want to subscribe and keep up with all the latest trends!";
					$subAnswer2 = $isFrench ? "Non merci!" : "No thanks!";
					$name = "v" . $compteurVolet . "q" . ($compeurQuestion + 1);
					$name1 = $name . "r" . "1";
					$name2 = $name . "r" . "2";
					$name3 = $name . "r" . "3";
					$name4 = $name . "r" . "4";
					$name5 = $name . "r" . "5";
					$nameSub = "v" . $compteurVolet . "q" . ($compeurQuestion + 2);
					$nameSub1 = $nameSub . "r" . "1";
					$nameSub2 = $nameSub . "r" . "2";
					$result .= <<<HTML
					<h3>$texte</h3>
					<div>
						<p>$whatCoordo</p>
						<p class="oblig">* $oblig</p>
						<label for="$name1">$lastName <span class="oblig">*</span></label>
						<input type="text" name="$name1" id="$name1" placeholder="$placeholder" required onfocus="putBackgroundBack(this)">
						<label for="$name2">$firstName <span class="oblig">*</span></label>
						<input type="text" name="$name2" id="$name2" placeholder="$placeholder" required onfocus="putBackgroundBack(this)">
						<label for="$name3">$email <span class="oblig">*</span></label>
						<input type="text" name="$name3" id="$name3" placeholder="$placeholder" required onfocus="putBackgroundBack(this)">
						<label for="$name4">$phone <span class="oblig">*</span></label>
						<input type="text" name="$name4" id="$name4" placeholder="$placeholder" required onfocus="putBackgroundBack(this)">
						<label for="$name5">$city <span class="oblig">*</span></label>
						<input type="text" name="$name5" id="$name5" placeholder="$placeholder" required onfocus="putBackgroundBack(this)">
					</div>
					<h3>$sub</h3>
					<div class="same-line">
						<input type="radio" name="$nameSub" id="$nameSub1" onclick="putParentBackgroundBack(this)">
						<label for="$nameSub1">$subAnswer1</label>
						<input type="radio" name="$nameSub" id="$nameSub2" onclick="putParentBackgroundBack(this)">
						<label for="$nameSub2">$subAnswer2</label>
					</div>
HTML;
				}

				$result .= <<<HTML
					<div class="same-line">
HTML;

				if ($compteurVolet > 1) {
					$result .= <<<HTML
						<input type="button" onclick="goToPrev($compteurVolet)" value="$prev">
HTML;
				}

				if (empty($volet["coordo"]) && $compteurVolet != sizeof($volets)) {
					$result .= <<<HTML
						<input type="button" onclick="goToNext($compteurVolet)" value="$next">
HTML;
				}
				else {
					$result .= <<<HTML
						<input type="button" onclick="send($compteurVolet)" value="$send">
HTML;
				}

				$result .= <<<HTML
					</div>
				</div>
HTML;
			}

			$result .= <<<HTML
			</div>
HTML;
		}

		return $result;
	}
