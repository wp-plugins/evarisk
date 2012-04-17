=== Evarisk ===
Contributors: Evarisk
Tags: Evaluation des risques, document unique, risques professionnels, audit risques, risques humains
Donate link: http://www.evarisk.com/document-unique-logiciel
Requires at least: 2.9.2
Tested up to: 3.3.1
Stable tag: 5.1.5.2

Avec le plugin "Evarisk" vous pourrez réaliser, de façon simple et intuitive, le ou les documents uniques de vos entreprises

== Description ==

Avec le plugin "Evarisk" vous pourrez réaliser, de façon simple et intuitive, le ou les documents uniques de vos entreprises et gérer toutes les données liées à la sécurité de votre personnel.
Veillez à sauvegarder le plugin avant de faire la mise à jour



== Installation ==

L'installation du plugin peut se faire de 2 façons :

* Méthode 1

1. Téléchargez `evarisk_VX.X.X.zip`
2. Uploader le dossier `evarisk` dans le répertoire `/wp-content/plugins/`
3. Activer le plugin dans le menu `Extensions` de Wordpress

* Méthode 2

1. Rechercher le plugin "Evarisk" à partir du menu "Extension" de Wordpress
2. Lancer l'installation du plugin



== Frequently Asked Questions ==

Question 1 : Le logiciel d'evarisk est t'il totalement gratuit ?

Oui le logiciel est totalement gratuit, il est publié sous une licence Publique Générale Affero (GNU). Ce programme est libre et gratuit en (Open Source), vous pouvez le redistribuer et/ou le modifier selon les termes de la Licence Publique Générale Affero GNU publiée par la Free Software Foundation.

Question 2 : La version 4.3.4 du logiciel va t'elle continué d'évoluer ?

Non, cette version est stable mais nous ne la ferons plus evoluer. Nous étions trop souvent bloqué sur des principes theoriques c'est la raison pour laquelle nous avons lancé la version 5

Question 3 : Peut t'on importer les informations a partir du logiciel en version 4.3.4

Nous n'avons pas prévu cet import facilement si vous souhaitez plus de renseignements contactez nous sur ce point.


== Screenshots ==

1. Tableau de bord du logiciel digirisk
2. Interface de gestion des options du logiciel digirisk
3. Interface d'import des utilisateurs
4. Liste des groupes du logiciel digirisk. Groupes d'utilisateurs: personnes affectées à un groupement/unité de travail/etc. Groupes d'évaluateurs: personnes participant à l'évaluation des risques
5. Interface d'ajout/édition d'un groupe (utilisateur et évaluateur)
6. Liste des préconisations existantes
7. Interface d'édition d'une famille de préconisation (avec les options pour l'impression dans les différents documents)
8. Interface d'édition d'une préconisation
9. Liste des méthodes d'évaluation
10. Interface d'édition des méthodes d'évaluation
11. Interface de gestion des catégories de danger et des dangers
12. Interface d'évaluation des risques: Gestion des informations concernant la société, ajout de photos, géolocalisation
13. Interface d'évaluation des risques: évaluation des risques, affectations des utilisateurs/évaluateurs/groupes/préconisations
14. Interface d'évaluation des risques: Génération des documents (document unique/fiche de pose)
15. Interface d'évaluation des risques: édition d'un risque
16. Interface d'évaluation des risques: Vue d'ensemble sur les risques et actions prioritaires affectées à ce risque (Disponible dans les groupements uniquement avec les risques des sous-éléments)
17. Interface d'évaluation des risques: Ajout d'un risque depuis une photo
18. Interface de prise de notes (Un fichier par utilisateur accessible depuis toutes les interfaces)
19. Interface de gestion des actions correctives


== Changelog ==

* Veillez à bien sauvegarder vos données avant d'effectuer une mise à jour du plugin


= Version 5.1.5.2 =

* ST319 - Ré-encodage du fichier readme

Améliorations

* ST317 - Possibilité de chosir un email personnalisé dans l'import des utilisateurs même si on a des paramètres génériques 
* ST318 - Possibilité de réinitialiser l'arbre des groupements depuis la corbeille(Remet tous les groupements au même niveau dans l'arbre) 


= Version 5.1.5.1 =

Corrections

* Parenthèse provoquant une erreur fatale dans le fichier d'envoi de photo
* Lors de la mise à jour de la base de données, la table des fiches n'était pas correctement renommée


= Version 5.1.5.0 =

Améliorations

* ST310 - Ajout du contenu de modifications apportées dans les tâches et sous-tâches dans les email envoyés aux utilisateurs associés
* ST309 - Demande d'actions corrective dans le front (- Affectation a un élément de l'arbre - Ajout d'une photo marquée comme "avant")
* ST312 - Possibilité de vérifier les opérations apportées sur la base de données lors des mises à jour automatique
* ST315 - Début de mise en place de la fiche salarié dans la partie administration

Corrections 

* ST173 - Les UT étaient présentes dans les DUER et dans la vue d'ensemble même si celle ci étaient supprimées 
* ST311 - Erreurs d'affectation des tâches si on cliquait sur enregistrer sans cliquer sur un élément 
* ST313 - La mise à jour automatique de la base de données ne se faisait pas correctement (Si le nombre de version entre la base de données existante et  la base de données de la nouvelle version était trop important alors certaines mise à jour ne se lançaient pas) 
* ST316 - Mise à jour de la librairies jquery ui en version 1.8.18 (Causait un conflit avec le plugin Advanced custom field) 


= Version 5.1.4.9 =

Améliorations 

* ST306 - Interface de liaison entre une tâche ou sous-tâches simplifiée 
* ST308 - Possibilité de demander une action corrective depuis la partie frontend du portail


Corrections 

* ST301 - Requêtes sur les listes des utilisateurs sur le tableau de bord ne prennent pas en compte les groupes d'utilisateurs (Problème de statut des enregistrements non pris en compte dans les requêtes) 
* ST302 - Dans les fiches de groupements impression des risques du groupement courant uniquement (Ceux des sous éléments étaient également imprimés) 

= Version 5.1.4.8 =

Corrections

* Suppression de l'affichage d'un champs dans les options


= Version 5.1.4.7 =

Améliorations

* ST249 - Copier/Déplacer les risques d'un élément à un autre 
* ST298 - Restauration des éléments de la corbeille (Possibilité de restaurer les éléments récursivement/Impossibilité de restaurer des éléments de type différents au même niveau) 
* ST295 - Ajout de l'identifiant de l'élément dans la box de récapitulatif des groupement et unité de travail 
* ST299 - Ajout d'un lien vers l'élément modififé dans les mails envoyés lors des modifications des taches et sous taches 

Corrections

* ST43 - Droits d'accés aux fichiers lors de l'installation sur un serveur(Correction en utilisant la commande exec de php) 
* ST293 - Lors de l'affectation des utilisateurs aux tâches et sous-tâches il y avait un affichage de données non souhaitable 
* ST294 - Génération du fichier de protection du site (A chaque fois que les options étaient enregistrées, un fichier de sauvegarde était créé avec le code de limitation d'accès et lorsqu'on désactivait la limitation le fichier htaccess était conservé avec la limitation mais le fichier de mot de passe était supprimé ce qui provoquait une erreur 403) 
* ST296 - La somme totale des risques d'un élément était affichée comme étant à zéro lors de la sélection de cet élément même si la somme était supérieure 
* ST297 - Corbeille impossible à restaurer si plusieurs éléments du même type présent (Les en-têtes des colonnes s'affichaient plusieurs fois et provoquaient une erreur javascript qui bloquait l'accès au bouton de sauvegarde) 


= Version 5.1.4.6 =

Corrections

*	Gestion de l'arbre défaillant depuis la version 3.3.1 de wordpress. Impossibilité de déplacer des éléments
* Vérification que le nom d'une nouvelle tâche soit bien rempli avant sa création


= Version 5.1.4.5 =

Améliorations

* Affichage du demandeur et de la date de la demande d'une tâche
* Envoi des fichiers dans les actions correctives (Gestion des extensions de fichiers autorisées depuis la configuration du plugin dans l'onglet "action corrective")
* Possibilité de bloquer l'accés au site grâce à un fichier htaccess depuis les options du logiciel dans la partie utilisateur
* Ajout d'un bouton permettant d'accéder à la liste des utilisateurs inscrit/ayant ou n'ayant pas participé à l'audit
* Possibilité de notifier les utilisateurs affectés aux tâches et sous-tâches pour chaque action effectuée

Corrections

* Droits d'accés à l'import des utilisateurs
* La visualisation des détails d'une sous-tâches depuis le suivi des actions correctives était défaillant
* Le démarquage de la photo après l'action était impossible

Divers

* Déplacement des menus de configurations dans la partie réglages de wordpress (En prévision de la refonte des menus)


= Version 5.1.4.4 =

Améliorations

* Possibilité de visualiser un graphique pour l'évolution d'un risque 
* Possibilité d'enlever le responsable des tâches et sous-tâches 

Corrections

* Une exception javascript était lancée lors de la création d'une action corrective (Le formulaire avec un enctype défini pour envoyer des fichiers ce qui posait problème à la réception) 
* Le click sur un utilisateur dans la liste permettant de choisir un responsable pour les tâches et sous-tâches d'un groupement ne fonctionnait pas 


= Version 5.1.4.3 =

Améliorations

* Suppression de l'option permettant de choisir de ne pas créer une sous-tâche pour les actions prioritaires (La création est obligatoire) 
* Reprise de l'ergonomie d'une partie de la gestion des actions correctives 
* Lors de l'enregistrement des options, on revient sur l'onglet courant 
* Option Actions correctives avancées remontée en haut de la liste 
* Gestion des droits sur les actions correctives 
* Possibilité d'avoir des photos sur les tâches

Corrections

* Lorsqu'on met un double guillemet dans le nom d'une tâche, il supprime ce qui suit (Correction de la librairie de gestion des input) 
* Impossibilité d'ajouter des photos avant et aprés lors d'une demande d'action corrective en mode avancé 
* Affichage parasite dans la box de création d'une tache ou d'une sous-tâche 
* Prise en compte du responsable pour les tâches 
* Modification du choix du responsable dans les actions correctives non avancées 
* Appel d'une mauvaise fonction pour la traduction _() au lieu de __() 
* Suppression de la possibilité d'ajouter un commentaire vide sur une tâche et une sous-tâche 
* Suppression de la possibilité de modifier certaines informations dans les tâches soldées avec l'option de modification â non(utilisateurs / notes / photos)


= Version 5.1.4.2 =

Améliorations

* L'onglet des options pour les produits ne s'affiche plus si le plugin wpshop n'est pas activé 
* Affichage de la date d'ajout d'une tâches et d'une sous-tâches qui peut être différente de la date de début 
* Modification de la façon de choisir un responsable pour les tâches et actions (Reprise du bloc standard de sélection d'un utilisateur) 

Corrections

* Impossibilité de créer un rôle dans digirisk sans choisir de rôle existant à copier 
* Interface d'import des utilisateurs (Taille de l'interface pour les petites résolutions d'écran / Modification du comportement du bouton d'import suivant le champs texte / Suppression des accents et majuscules aux e mail et identifiant des utilisateurs importés / Correction du fichier modèle d'import avec les champs pour les accidents du travail) 
* Changement du comportement de l'interface d'édition d'un utilisateurs si tous les champs nécessaires aux accidents ne sont pas remplis 
* Affichage des noms des éléments dans l'arborescence principale quand un caractères spécial était présent 
* Message d'erreur lors de l'envoie d'une photo (Le message "Failed" s'affichait alors que la photo était bien envoyée)


= Version 5.1.4.1 =

Améliorations

* Gestion des accidents de travail 
* Modification de l'import des utilisateurs (Ajout de champs complémentaires facultatifs) 
* Ajout d'informations concernant les groupements (Siret / Siren / Type de groupement / Numéro de risque associé) 
* Changement de la gestion des options en ongle t(Les options sont rangées par catégories dans des onglets ce qui permet une meilleure lisibilité) 
* Intégration d'une gestion de documentation 

Corrections

* Problème de remontée d'information dans l'arborescence(La somme des risques n'étaient pas remontées correctement à cause des sous-groupements / La somme de cotation des risques n'étaient plus calculées) 


= Version 5.1.4.0 =

Corrections 

* Problème de sécurité lors de l'envoie de documents / photos


= Version 5.1.3.9 =

Améliorations

* Intégration de la nouvelle version de WP Shop


= Version 5.1.3.8 =

Corrections 

* Format des numéros de téléphones dans le DUER (Si on entrez des numéros avec des espaces, l'information enregistrée ne correspond a rien) 


= Version 5.1.3.7 =

Améliorations

* Ajout de la corbeille pour récupérer les éléments précédemment supprimés (Permettrait de récupérer des éléments qui auraient été supprimé par erreur.) 
* Mise à jour des informations des taches lorsqu'on solde la tache(On demande de changer les informations pour la tache courante et les sous-éléments) 
* Possibilité de modifier la position du marqueur dans la carte google map sur les différents éléments 
* La partie méthodologie du chapitre administratif est modifiable directement lors de la génération du DUER 

Corrections

* Lors de l'ajout d'une méthode, on ne peut pas définir comme photo par défaut 
* Attribution de la photo par défaut pour les catégories de danger (Lorsqu'on associe une photo par défaut a une nouvelle catégorie de danger, le chemin d'affichage n'était pas correct) 
* Préfixe de la table utilisateurs inscrit en dur (Impossibilité de récupérer les utilisateurs lorsqu'on installe avec un préfixe différent) 
* Mauvais comportement lors de l'enregistrement d'un risque en cochant la case historisation(Le risque était enregistré mais n'apparaissait pas alors qu'il devait apparaitre et faire disparaitre l'ancienne cotation) 
* Accents dans la description des groupements et/ou fiches de poste mal imprimés 
* Correction de la box d'affectation des droits quand aucun utilisateur 
* Gestion des dangers et catégories de danger 


= Version 5.1.3.6 =

Améliorations

*	Reprise de la box pour le suivi des actions correctives par risque dans l'évaluation des risques
* Ajout d'un fichier pour construire l'import des utilisateurs

Corrections

* Javascript indisponible lors de l'installation


= Version 5.1.3.5 =

Améliorations 

* Reprise de la page d'installation pour homogénéisation avec le reste du plugin 
* Ajout des identifiants aux éléments manquants (Actions correctives dans le suivi des actions / Risques dans la liste des risques déjà évalués / éléments dans les documents (DUER / FP / FGPT) / Groupes d'utilisateurs / Utilisateurs) 
* Personnalisation des identifiants (Ajout d'une option pour chaque type d'identifiant) 
* Informations générales intégrées (Description / Adresse / Téléphone) 
* Génération d'un document on retombe sur le bon onglet dans l'historique
* Ajout du lien pour télécharger le fichier contenant l'export au format txt des actions correctives

Corrections 

* Retour à la ligne dans le champs commentaire dprovoquait une erreur javascript (Ce qui empêchait l'utilisateur de modifier le contenu de la box) 


= Version 5.1.3.4 =

Améliorations

*	Nettoyage des javascripts inutiles
* Appel des javascripts et css uniquement lorsqu'on se trouve sur une des pages du plugin digirisk

Corrections

* Caractères indésirables dans le document unique


= Version 5.1.3.3 =

Améliorations

* Mise à jour des pictos pour la gestion des droits (voir - sélectionner tous/aucun - récursif)
* Mise en place d'une option pour le choix de l'action a effectuer lorsqu'on essaie de créer un élément dont le nom existe déjà

Corrections

* Compatibilité avec la version 3.2.1 de wordpress (jquery)
* Affectation des groupes d'utilisateurs aux éléments
* Recherche des utilisateurs dans les box d'affectation
* Génération des fiches de poste et des fiches de groupement


= Version 5.1.3.2 =

Améliorations

* Interface d'affectation des droits des utilisateurs dans chaque élément de l'arbre
* Ajout de l'affectation récursive des droits des utilisateurs
* Affichage des droits spécifiques des utilisateurs de façon "human-readable"
* Ajout de la possibilité de générer les fiches de groupements (calqué sur les fiches de poste sans les préconisations)

Corrections

* Correction pour les droits utilisateurs pour la box d'édition d'un groupement ou d'une unité de travail, le bouton enregistrer était présent même si les droits étaient en lecture seule
* Erreur javascript dans la box d'affectation des droits dans chaque élément de l'arbre si aucun utilisateur n'avait été créé
* Génération des fiches de poste en masse qui ne fonctionnait plus sur les groupements
* Enregistrement du domaine des emails des utilisateurs dans les options suite au changement de gestion des options
* Renommage de la catégorie de préconisations anciennement nommée "recommandation" en "avertissements" (CP3)
* Corrections de l'interfaçage de wpshop et de digirisk


= Version 5.1.3.1 =

Améliorations

* Gestion des droits des utilisateurs

Corrections

* Box des préconisations affectées à un élément du à l'option sur l'efficacité


= Version 5.1.3.0 =

Améliorations

*	Réduction de la taille des screenshots

Corrections

* Fautes d'orthographe dans les fiches de postes
* Fautes d'orthographe dans la partie concernant les utilisateurs participant à l'évaluation
* Prise en compte des commentaires sur les risques lors de la génération du document unique
* Caractère &rsquo; qui générait une erreur lors de l'ouverture du document open office


= Version 5.1.2.9 =

Améliorations 

* Reprendre le modèle du document unique(Intégration du plan d'action en première version: avec les actions correctives prévues) 
* Les boutons d'enregistrement dans l'interface vue d'ensemble sont toujours visible 
* Reprise de la gestion pour reprendre les bonnes pratiques de wordpress (Tout est stocké dans une seule option pour le moment, à voir pour la performance et à splitter en plusieurs options si on voit des problème de performance dans les différentes interface utilisant ces options) 
* Intégration de la gestion des produits (Ajout d'une option permettant de sélectionner les catégories a afficher:Cette option va prendre la liste des catégories dans le plugin wpshop développé par la société eoxia.La liste sera disponible uniquement si le plugin wpshop est activé dans le wordpress courant)
* Ajout d'une box dans l'évaluation des risques pour affecter les produits aux éléments
* Possibilité de filtrer les résultats de la box par catégorie
* Recherche instantanée de produits
* Conservation d'informations sur le produit: Nom du produit, identifiant du produit, date de dernière modification du produit, nom de la ou des catégorie(s), identifiant de la ou des catégories. Si le produit n'a jamais été utilisé dans le plugin, il sera inséré sinon on compare les dates de modifications si le produit a été modifié depuis l'insertion dans digirisk alors on fait une nouvelle insertion
* Modification des interfaces de gestion des groupes(Reprise des interfaces d'affectation d'utilisateurs à un élément) 
* Transférer les données des anciennes aux nouvelles tables(Options : de la table evarisk à la table wp / Utilisateurs : Liste des groupes (utilisateur et évaluateur) vers la nouvelle table unique de groupes / Utilisateurs : Liste des utilisateurs affectés aux groupes dans la table de liaison entre un élément et un utilisateur / Utilisateurs : Liste des groupes affectés aux éléments vers la nouvelle table / Version de la base de données transférée dans la table des options de wordpress dans une option digirisk_db_option)
* Un clic droit ou gauche dans la vue d'ensemble coche la ligne pour modification 
* Déplacement des tables non-utilisées vers wp_evatrash__(option/ppe/ppe_use/eav/users/roles/version/user_group/user_evaluator) 
* Ajout de l'identifiant de l'élément des les listing(vue d'ensemble/document unique) 
* Intégration des options dans la table des options de wordpress lors d'une nouvelle installation 
* Ajout du bouton de régénération des documents unique manquants 
* Ajout du bouton de suppression d'un document unique 

Corrections

* Remplacer les br dans l'interface de vue d'ensemble 
* L'activation de l'efficacité dans les préconisations désactive tous les clics(Lorsque l'option "efficacité pour les préconisations" était activée alors on ne pouvait plus ajouter de préconisations/La prise en compte de l'efficacité lors de la modification de la préconisation ne se faisait pas) 
* Erreur fatale lancée à la génération d'un document (DUER ou fiche de poste) due à la suppression d'un segment du fichier (Modification de la librairie odt) 
* Suppression d'un utilisateur de la liste des utilisateurs(L'hirondelle ne disparaissait pas) 


= Version 5.1.2.8 =

Améliorations

* Possibilité de chosir un modèle pour la génération des fiches de poste pour un groupement entier
* Possibilité de renseigner / modifier les informations suivantes pour la génération du document unique : localisation de l'étude, remarque importante, source
* Regroupement des historiques des documents générés pour un groupement (document unique et fiche de poste) dans un même onglet
* Regroupement des formulaires de génération de documents (document unique et fiche de poste) dans un même onglet (Géérer le bilan)
* Ajout du numéro de la version actuelle dans l'appel des css et des javascripts pour éviter d'avoir à effectuer un ctrl+f5 lors d'une mise à jour du plugin
* Mise en conformité des appels css et javascript avec les bonnes pratiques de wordpress
* Ajout d'une interface permettant de modifier les commentaires et les descriptions des actions correctives prioritaires pour tous les risques de tous les éléments situés sous l'élément courant (uniquement disponible sur les groupements)

Corrections

* Appel de la fonction EvaDisplayDesign::afficherFinPage() manquant dans deux interface provoquant des erreurs au niveau du code html généré
* Erreur lors de l'affichage du suivi des actions correctives en mode avancé (Problème avec la notion de quotation avant et aprés le risque non géré lors de l'ajout d'une action corrective en même temps que le risque)
* Modification de certains scripts et de la fonction eva_tools::IsValid_Variable() pour remplacer le caractère &rsquo; par ' qui génére une erreur lors de la génération des documents en odt
* Problème de nommage du fichier zip contenant la liste des fiches de poste pour un groupement, qui faisait que la liste des fichiers zip n'était pas affichée après génération
* Problème lors du rechargement de la box "bilan" des groupements le clic sur le boutton de génération du document unique ne fonctionnait plus


= Version 5.1.2.7 =

Améliorations

*	Mise en place des préconisations sur les unités de travail
* Ajout des préconisations dans les fiches de poste
* Possibilité de définir l'affichage voulu pour les familles de préconisations et les préconisations (texte + image, texte seulement, image seulement + taille de l'image)
* Ajout d'un gestionnaire de notes par utilisateur. Pour le moment au format texte.

Corrections

* Lorsqu'on supprime un pattern dans le modèle odt (exemple: {photoDefaut}) il n'y a plus d'exception lancée par la librairie
*	Le modèle par défaut est à nouveau sélectionné lorsqu'il n'y a pas d'autre modèle


= Version 5.1.2.6 =

Améliorations

*	Possibilité d'ajouter un modèle odt pour les fiches de poste
* Prise en compte de la photo principale d'une unité de travail dans la fiche de poste
* Possibilité de changer la taille de la photo générée dans les fiches de poste à travers une option

Corrections:

* Correction du déplacement d'une untié de travail (erreur fatale lancée)


= Version 5.1.2.5 =

* Déplacement des fichiers envoyés et générés du dossier du plugin vers le dossier wp-content de wordpress (pour éviter qu'ils ne soient supprimés lors de la mise à jour)

Améliorations:

* Gestion des statuts des tâches et sous tâches des action correctives (non-commencée / passer en cours)
* Prise en compte des deux types d'affichage (pageHook) pour les box des groupements (groupement gestion / groupement risques)
* Ajout de statistiques sur le tableau de bord (personnel/risque)
* Génération de fiches de postes simple (par poste et par groupement)
* Génération du plan d'action au format texte dans le document unique 

Corrections:

* Mise à jour des tâches parentes lors de la mise à jour des enfants
* Email des utilisateurs mal construit à l'import avec le formulaire d'insertion rapide (prenom.prenom au lieu de prenom.nom)
* Le champs affichés dans la box "récapitulatif" des unités et groupements n'affichait pas le nom en entier si il était trop long

Ergonomie:

* Le clic n'importe où sur la ligne édite l'élément plus forcément sur le nom de l'élément
* Ajout d'un identifiant sur la ligne des éléments dans les arbres


= Version 5.1.2.4 =

Améliorations:

* Homogénéisation du formulaire d'ajout de risque (simple/par photo)

Corrections:

* Problème lors de la création de l'arborescence de stockage des fichiers odt générés pour les documents uniques


= Version 5.1.2.3 =

Améliorations:

* Ajout d'un bouton cocher/décocher tout dans la box des utilisateurs
* L'affichage du bouton "enregistrer" de la box des utilisateurs s'affichent en fonction des actions dans la box
* Suppression du footer (doublon) dans les tableaux listant les risques unitaires et par unités
* Ajout des préconisations pour les risques
* Rechargement de l'arbre après un ajout ou une modification d'action corrective
* Affichage des dates dans les tâches "mères"

Corrections:

* Icone jquery ui verte ne s'affiche pas à cause du nommage dans le fichier css
* Erreur dans le fichier evaDisplayDesign.class.php ligne 2370 appel de _() au lieu de __()

Ergonomie:

* Interface de gestion des options du logiciel


= Version 5.1.2.2 =

Améliorations :

* Ajout de la box permettant d'affecter des utilisateurs à un élément (groupement, unité de travail, évaluation, ...)
* Changement de l'ergonomie de la box d'affectation des utilisateurs à un élément
* Ajout d'une interface pour ajouter des utilisateurs rapidement à partir d'un nom et d'un prénom
* Supprimer l'affichage de la box bilan sur unité de travail
* Remonter au niveau de la box des risques après sauvegarde
* Traduction des Datatables

Corrections:

* Correction de bugs de la box d'affectation des utilisateurs à un élément
* Vérification du rechargement après ajout/edition/suppression d'un élément
* Vérifier le comportement de l'interface après un drag and drop
* Vérifier le comportement lors de la suppression d'un groupement
* Enregistrement du drag and drop d'un groupement

Ergonomie:

* Amélioration ergonomie de la box de liaison entre un utilisateur et un élément
* Améliorer l'ergonomie de l'édition du nom de l'élément dans la box récapitulatif
* Changer la taille des pictos dans les arbres

= Version 5.1.2.1 =

Corrections:

* Intégration de la librairie de gestion documentaire manquante lors de la dernière mise à jour
* Correction de la box d'affectation des utilisateurs à une unité de travail ou à un groupement
* Passage sur l'onglet historique des documents uniques après génération du document unique

= Version 5.1.2 =

Améliorations :

* Changement du nom des documents uniques dans l'historique : rajout du numéro de version pour différencier des documents générés le même jour et ayant le même nom
* Vérification du nombre de sous éléments et du type de ces éléments dans les arbres pour ne pas afficher les bouton d'ajout dès le départ et non en javascript une fois la page chargée
* Sécurisation de l'insertion des différents élément (UT et Groupement) pour ne pas pouvoir insérer une UT au même niveau qu'un groupement
* Possibilité d'ajouter des risques associés à des photos
* Gestion des modèles de document unique par groupement
* Duplication d'un modèle de document unique .odt d'un groupement à un autre
* Ajout du pourcentage d'avancement dans l'arbre des actions correctives

Corrections :

* Changement de la variable $() en evarisk() pour tous les javascript utilisant jQuery pour éviter tout conflit avec d'autres extensions
* Image "supprimer" de l'interface de gestion des groupes d'utilisateurs et d'évaluateurs plus grosse que les autres
* Suppression de la possibilité de déplacer un élément "parent" dans un de ses "enfants"
* Envoi de photos sur un serveur (impossibilité à cause des droits d'accès au dossier)

Ergonomie:

* Masquage de la liste des catégories de danger lors d'une modification de risque
* Changement de l'emplacement du bouton supprimer des box "groupes d'évaluateur" et "groupes d'utilisateurs" (de la première colonne à la dernière)

= Version 5.1.1 =

* Saut de version en 5.1.2


= Version 5.1.0.2 =

Corrections:

* Bug dans la galerie photo pour la photo par défaut


= Version 5.1.0.1 =

Corrections:

* Problème avec la box des utilisateurs participant à l'évaluation lorsqu'il n'y avait aucun utilisateur


= Version 5.1.0 =

Améliorations :

* Possibilité de ne pas faire apparaitre une modification de la quotation des risques dans les historiques
* Possibilité de connaitre le créteur d'une tâche et d'une action
* Possibilité de connaitre le soldeur (personne qui dit que la tâche ou action est terminée) d'une tâche et d'une action
* Possibilité de connaitre la date ou une tâche et/ou une action ont été soldées
* Possibilité d'affecter un utilisateur comme responsable d'une tâche et d'une action
* Possibilité d'affecter un ou plusieurs utilisateurs comme acteurs d'une tâche et d'une action
* Mise en place de l'option responsable de tâche et d'action obligatoire
* Ajout d'une interface de gestion pour les options du logiciel
* Mise en place d'un slider pour l'avancement d'une action
* Mise en place de la box qui permet de savoir à quel élément est affecté une tâche dans l'interface des actions correctives
* Possibilité d'affecter une tâche à un groupement, une unité de travail ou à un risque
* Mise en place de l'interface contenant les différentes actions correctives liées à un risque lors de la modification de ce dernier
* Ajout du statut "Done" (soldé) et "DoneByChief" (soldé par le supérieur) dans les tâches et actions
* Lors du passage à 100 pour la progression d'une action on met à jour le statut de l'action à "Done"
* Ajout d'options pour laisser la possiblité de modifier une tache ou une action même si celle ci et déjà soldée
* Possibilité de solder une tâche sans que tous ses sous-éléments soient soldé (On solde tous ses sous-éléments en cascade)
* Possibilité d'affecter des utilisateurs à la réalisation d'une tâche et d'une action
* Liaison d'une tâche à un risque lors de sa modification (Option ne pouvoir affecter uniquement les taches soldées)
* Mise à jour de la progression d'une tâche lors de la modification d'une action
* Mise en place d'un suivi sur les tâches et actions.
* Reprise du suivi des actions correctives avec la quotation avant la création de l'action correctives et la quotation après.
* Avertissement qu'il existe des actions correctives non soldées lors de la modification d'un risque
* écran taches à solder parce que toutes les sous-actions sont soldées
* écran taches/actions dont la date est dépassée mais qui ne sont toujours pas soldées
* écran taches/actions soldées avec les risques a ré-évaluer
* Reprise de l'échelle de quotation pour rendre la quotation plus objective et pour correspondre à la norme du fichier odt
* Ajout d'une option d'auto-installation dans le cas où la version utilisée est la version executable
* Possibilité de supprimer une tache ou une action
* Affichage de la box risque dans l'évaluation des risques
* Changement du séparateur par défaut du fichier pour l'import des utilisateurs
* Changement du role par défaut pour les utilisateurs importés
* Affichage des extensions autorisées pour les fichiers d'import d'utilisateurs
* Possibilité d'ajouter des photos aux actions correctives et de définir les photos avant et apré l'action
* Option actions correctives avancée pour actions correctives simplifiée par défaut
* Déplacement du menu principal du plugin pour le remonter juste en dessous du Tabelau de bord de Wordpress
* Ajout d'informations sur les champs obligatoire du formulaire des actions correctives
* Ajout d'un lien pour remplir les champs de date des actions correctives avec la date d'aujourd'hui
* Changement de la gestion de la galerie photo
* Onglet de suivi simplifié
* Gestion simplifiée des photos avant et aprè les actions correctives dans le mode simple

Corrections :

* Problème d'affichage lors de l'édition d'un élément sous internet explorer
* Erreur lors de la création de la table de liaison entre les risques et les tâches des actions correctives
* Nom de la table methode marqué "en dur" dans le script d'insertion de données
* Suppression de la possibilité de créer une unité de travail avant tout groupement
* Problème lors de la mise à jour du coût pour les actions
* Division par zéro dans le suivi des actions corectives lorsqu'aucune date n'a été rentrée
* Erreurs php affichées lors de la création du document unique
* Caractères spéciaux à la génération du document unique au format odt
* Changement de la façon de créer la référence du document unique
* Suppression de l'espace superflu lorsqu'on demande la génération du document unique (espace du aux champs datepicker)
* Accent de "Décembre"
* Pictos des fléches qui ne s'affichaient plus à cause de leur déplacement
* Bouton "enregistrer" de la box de liaison entre utilisateur et action qui ne s'affichait pas correctement à cause d'un mauvais flag à la création
* Champs manquants pour les photos avant et après les actions correctives
* Lors d'une erreur de mise à jour sur les photos avant et après les actions correctives le picto n'était pas bon
* Raccourcissement de la barre de progression d'une action corrective

= Version 5.0.3 =

Améliorations :

* Import d'utilisateurs en masse
* Affichage des dangers, méthodes et explications de la méthode plus claire dans la box d'évaluation des risques

Corrections :

* Corrections de liens cassés sous internent explorer pour l'évaluation des risques

Modifications:

* Masquage de la box "bilan" dans les unités de travail


= Version 5.0.2 =

Améliorations :

* Génération du document unique au format Open Office .odt
* Possibilités d'ajouter une image explicative pour les méthodes d'évaluation. Qui s'affichera au niveau des slider d'évaluation
* Historique des documents unique générés, avec possibilité de les afficher dans le navigateur
* Indicateur de chargement lors du changement d'onglet dans la box bilan de l'évalutation des risques
* Liens de téléchargement des fichiers odt générés à droite du formulaire
* Affichage de l'historique des documents unique
* Façon de générer le nom du document unique dans le formulaire
* Récupération du nom du groupement actuel pour remplir le champs nomSociete du document unique

Ergonomie :

* Chargement de la partie évaluation des risques lors du clic sur une ligne
* Suppression (par masquage) du bouton de lancement d'évaluation des risques
* Bouton "supprimer" déplacé en fin de ligne dans l'arbre des éléments
* Suppression du rechargement de la partie gauche lors de l'édition d'un élément

Corrections :

* Changement des descriptions pour la livraison sur le svn de wordpress
* Harmonisation de la taille sur le picto categorie de danger
* Mise à jour du curseur sur les liens
* Mise à jour du lien vers la page du plugin et de la description courte
* Homogénéisation des pictos
* Mise à des informations affichées pour le theme
* Suppression d'une parenthèse en trop dans le readme.txt
* Si aucun nom est donné pour la génération du document unique alors création automatique sur la base de YYYYMMDD_documentUnique_NomSociete
* Création picto voiture et mise en place picto risques manutention manuelle

Modification structurelle :

* Changement du format de stockage des champs groupes utilisateurs/risques unitaires/risques par unité.
* Ajout d'un champs de statut sur les documents uniques

Améliorations code :

* Factorisation de certaines méthodes (Arborescence des groupements et unités de travail / affichage de la galerie)
* Reprise de la variable globale pour afficher le picto de suppression dans certains scripts
* Ajout de commentaires dans le code



= Version 4.3.4 vers la Version 5.0.1 =

* Premiere version en béta

Dans cette premiere version nous avons intégré de nombreuses fonctionnalités par rapport à la version 4.3.4. Tous les fondamentaux ont été revus :

Méthode d'évaluation :

* Le logiciel est capable d'ingérer toutes les méthodes d'évaluation des risques et de transcrire le résultat sur une échelle de 0 à 100 avec un pas de 1
* La possibilité d'intégrer vos méthodes d'évaluations (pour les personnes avertis)
* La méthode d'évaluation des risques implantée par défaut est basée sur la méthode de kinney agrémentée de 2 facteurs supplémentaires


La mise en place des dangers :

* Cette nouvelle version vous permettra de gérer tous vos dangers et leur catégories
* Vous retrouverez, la segmentation de l'INRS dans l'ED840

La gestion multi-société ou multi-services :

* Vous pouvez maintenant gérer des structures importantes dans le logiciel et imprimer votre document unique à chaque niveau de votre structure.
Ceci vous permet, par exemple, de gérer une holding avec 5 sociétés et 25 Services et de gérer vos unités de travail a l'unité ou sous le groupement souhaité en fonction des rattachments de responsabilités.
Pour les administrations cela vous permettra de calquer vos évaluations des risques sur l'organigramme et de réaliser vos documents d'évaluations par services.

L'intégration à Wordpress :

* L'intégration à wordpress fait suite de nombreuses demandes de communication des services des gestions des risques. Worpress est aujourd'hui un des logiciels permettant de gérer le plus simplement et avec la meilleure fiabilité un site internet.

* Ce site pourra etre déployé en interne (intranet) et/ou en externe en version hébergé  ce qui permettra de suivre toutes les actions du service prévention. La communication des responsables sécurité sera de fait extremement simplifiée.


Voici les 4 changements fondamentaux qui permettent aujourd'hui à ce logiciel de ce mettre en place sur des infrastructures de plusieurs milliers de personnes tout en améliorant la convivialité de départ pour les petites entreprises.



== Améliorations Futures ==



== Upgrade Notice ==

* Version 4.3.4 vers la version 5.0.1

Nous n'avons pas prévu de reprise des données de la plateforme 4.3.4 pour le moment si vous souhaitez plus d'information n'hesitez pas a nous contacter


== Contactez l'auteur ==

dev@evarisk.com