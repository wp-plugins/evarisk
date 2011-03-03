=== Evarisk ===
Contributors: Evarisk
Tags: Evaluation des risques, document unique, risques professionnels, audit risques, risques humains
Donate link: http://www.evarisk.com/document-unique-logiciel
Requires at least: 2.9.2
Tested up to: 3.0.4
Stable tag: 5.1.2.5

Avec le plugin "Evarisk" vous pourrez r&eacute;aliser, de fa&ccedil;on simple et intuitive, le ou les documents uniques de vos entreprises

== Description ==

Avec le plugin "Evarisk" vous pourrez r&eacute;aliser, de fa&ccedil;on simple et intuitive, le ou les documents uniques de vos entreprises et g&eacute;rer toutes les donn&eacute;es li&eacute;es &agrave; la s&eacute;curit&eacute; de votre personnel.
Veillez &agrave; sauvegarder le plugin avant de faire la mise &agrave; jour



== Installation ==

L'installation du plugin peut se faire de 2 fa&ccedil;ons :

* M&eacute;thode 1

1. T&eacute;l&eacute;chargez `evarisk_VX.X.X.zip`
2. Uploader le dossier `evarisk` dans le r&eacute;pertoire `/wp-content/plugins/`
3. Activer le plugin dans le menu `Extensions` de Wordpress

* M&eacute;thode 2

1. Rechercher le plugin "Evarisk" &agrave; partir du menu "Extension" de Wordpress
2. Lancer l'installation du plugin



== Frequently Asked Questions ==

Question 1 : Le logiciel d'evarisk est t'il totalement gratuit ?

Oui le logiciel est totalement gratuit, il est publi&eacute; sous une licence Publique G&eacute;n&eacute;rale Affero (GNU). Ce programme est libre et gratuit en (Open Source), vous pouvez le redistribuer et/ou le modifier selon les termes de la Licence Publique G&eacute;n&eacute;rale Affero GNU publi&eacute;e par la Free Software Foundation.

Question 2 : La version 4.3.4 du logiciel va t'elle continu&eacute; d'&eacute;voluer ?

Non, cette version est stable mais nous ne la ferons plus evoluer. Nous &eacute;tions trop souvent bloqu&eacute; sur des principes theoriques c'est la raison pour laquelle nous avons lanc&eacute; la version 5

Question 3 : Peut t'on importer les informations a partir du logiciel en version 4.3.4

Nous n'avons pas pr&eacute;vu cet import facilement si vous souhaitez plus de renseignements contactez nous sur ce point.


== Screenshots ==

1. Interface gestion des m&eacute;thodes d'&eacute;valuation
2. Interface de gestion des dangers
3. Interface de gestion des groupes d'utilisateurs, ces groupes permettent de mettre en place des regroupements de personnes travaillant sur les memes unit&eacute;s de travail.
4. Modification d'un groupe utilisateur par glisser d&eacute;poser
5. Groupe d'&eacute;valuateurs interne et ou externe
6. Ecran de gestion des groupements, cette partie permet de g&eacute;rer la hi&eacute;rarchie et les groupes des unit&eacute;s de travail en services, sous service etc...la modification se fait par glisser d&eacute;poser
7. Ecran de modification de groupement avec la g&eacute;olocalisation des groupements dans google maps
8. Ecran d'&eacute;valuation des risques rajout et suppression d'un risque
9. Ecran d'ajout d'un risque avec la liste de l'INRS et 4 risques propre &agrave; l'entreprise, on voit les jauges qui permettent d'&eacute;valuer, Gravit&eacute;, Exposition, Occurrence, Formation, Protection.

== Changelog ==

* Veillez &agrave; bien sauvegarder vos donn&eacute;es avant d'effectuer une mise &agrave; jour du plugin

= Version 5.1.2.5 =

* D&eacute;placement des fichiers envoy&eacute;s et g&eacute;n&eacute;r&eacute;s du dossier du plugin vers le dossier wp-content de wordpress (pour &eacute;viter qu'ils ne soient supprim&eacute;s lors de la mise &agrave; jour)

Am&eacute;liorations:

* Gestion des statuts des t&acirc;ches et sous t&acirc;ches des action correctives (non-commenc&eacute;e / passer en cours)
* Prise en compte des deux types d'affichage (pageHook) pour les box des groupements (groupement gestion / groupement risques)
* Ajout de statistiques sur le tableau de bord (personnel/risque)
* G&eacute;n&eacute;ration de fiches de postes simple (par poste et par groupement)
* G&eacute;n&eacute;ration du plan d'action au format texte dans le document unique 

Corrections:

* Mise &agrave; jour des t&acirc;ches parentes lors de la mise &agrave; jour des enfants
* Email des utilisateurs mal construit &agrave; l'import avec le formulaire d'insertion rapide (prenom.prenom au lieu de prenom.nom)
* Le champs affich&eacute;s dans la box "r&eacute;capitulatif" des unit&eacute;s et groupements n'affichait pas le nom en entier si il &eacute;tait trop long

Ergonomie:

* Le clic n'importe o&ugrave; sur la ligne &eacute;dite l'&eacute;l&eacute;ment plus forc&eacute;ment sur le nom de l'&eacute;l&eacute;ment
* Ajout d'un identifiant sur la ligne des &eacute;l&eacute;ments dans les arbres


= Version 5.1.2.4 =

Am&eacute;liorations:

* Homog&eacute;n&eacute;isation du formulaire d'ajout de risque (simple/par photo)

Corrections:

* Probl&egrave;me lors de la cr&eacute;ation de l'arborescence de stockage des fichiers odt g&eacute;n&eacute;r&eacute;s pour les documents uniques


= Version 5.1.2.3 =

Am&eacute;liorations:

* Ajout d'un bouton cocher/d&eacute;cocher tout dans la box des utilisateurs
* L'affichage du bouton "enregistrer" de la box des utilisateurs s'affichent en fonction des actions dans la box
* Suppression du footer (doublon) dans les tableaux listant les risques unitaires et par unit&eacute;s
* Ajout des pr&eacute;conisations pour les risques
* Rechargement de l'arbre apr&egrave;s un ajout ou une modification d'action corrective
* Affichage des dates dans les t&acirc;ches "m&egrave;res"

Corrections:

* Icone jquery ui verte ne s'affiche pas &agrave; cause du nommage dans le fichier css
* Erreur dans le fichier evaDisplayDesign.class.php ligne 2370 appel de _() au lieu de __()

Ergonomie:

* Interface de gestion des options du logiciel


= Version 5.1.2.2 =

Am&eacute;liorations :

* Ajout de la box permettant d'affecter des utilisateurs &agrave; un &eacute;l&eacute;ment (groupement, unit&eacute; de travail, &eacute;valuation, ...)
* Changement de l'ergonomie de la box d'affectation des utilisateurs &agrave; un &eacute;l&eacute;ment
* Ajout d'une interface pour ajouter des utilisateurs rapidement &agrave; partir d'un nom et d'un pr&eacute;nom
* Supprimer l'affichage de la box bilan sur unit&eacute; de travail
* Remonter au niveau de la box des risques apr&egrave;s sauvegarde
* Traduction des Datatables

Corrections:

* Correction de bugs de la box d'affectation des utilisateurs &agrave; un &eacute;l&eacute;ment
* V&eacute;rification du rechargement apr&egrave;s ajout/edition/suppression d'un &eacute;l&eacute;ment
* V&eacute;rifier le comportement de l'interface apr&egrave;s un drag and drop
* V&eacute;rifier le comportement lors de la suppression d'un groupement
* Enregistrement du drag and drop d'un groupement

Ergonomie:

* Am&eacute;lioration ergonomie de la box de liaison entre un utilisateur et un &eacute;l&eacute;ment
* Am&eacute;liorer l'ergonomie de l'&eacute;dition du nom de l'&eacute;l&eacute;ment dans la box r&eacute;capitulatif
* Changer la taille des pictos dans les arbres

= Version 5.1.2.1 =

Corrections:

* Int&eacute;gration de la librairie de gestion documentaire manquante lors de la derni&egrave;re mise &agrave; jour
* Correction de la box d'affectation des utilisateurs &agrave; une unit&eacute; de travail ou &agrave; un groupement
* Passage sur l'onglet historique des documents uniques apr&egrave;s g&eacute;n&eacute;ration du document unique

= Version 5.1.2 =

Am&eacute;liorations :

* Changement du nom des documents uniques dans l'historique : rajout du num&eacute;ro de version pour diff&eacute;rencier des documents g&eacute;n&eacute;r&eacute;s le m&ecirc;me jour et ayant le m&ecirc;me nom
* V&eacute;rification du nombre de sous &eacute;l&eacute;ments et du type de ces &eacute;l&eacute;ments dans les arbres pour ne pas afficher les bouton d'ajout d&egrave;s le d&eacute;part et non en javascript une fois la page charg&eacute;e
* S&eacute;curisation de l'insertion des diff&eacute;rents &eacute;l&eacute;ment (UT et Groupement) pour ne pas pouvoir ins&eacute;rer une UT au m&ecirc;me niveau qu'un groupement
* Possibilit&eacute; d'ajouter des risques associ&eacute;s &agrave; des photos
* Gestion des mod&egrave;les de document unique par groupement
* Duplication d'un mod&egrave;le de document unique .odt d'un groupement &agrave; un autre
* Ajout du pourcentage d'avancement dans l'arbre des actions correctives

Corrections :

* Changement de la variable $() en evarisk() pour tous les javascript utilisant jQuery pour &eacute;viter tout conflit avec d'autres extensions
* Image "supprimer" de l'interface de gestion des groupes d'utilisateurs et d'&eacute;valuateurs plus grosse que les autres
* Suppression de la possibilit&eacute; de d&eacute;placer un &eacute;l&eacute;ment "parent" dans un de ses "enfants"
* Envoi de photos sur un serveur (impossibilit&eacute; &agrave; cause des droits d'acc&egrave;s au dossier)

Ergonomie:

* Masquage de la liste des cat&eacute;gories de danger lors d'une modification de risque
* Changement de l'emplacement du bouton supprimer des box "groupes d'&eacute;valuateur" et "groupes d'utilisateurs" (de la premi&egrave;re colonne &agrave; la derni&egrave;re)

= Version 5.1.1 =

* Saut de version en 5.1.2


= Version 5.1.0.2 =

Corrections:

* Bug dans la galerie photo pour la photo par d&eacute;faut


= Version 5.1.0.1 =

Corrections:

* Probl&egrave;me avec la box des utilisateurs participant &agrave; l'&eacute;valuation lorsqu'il n'y avait aucun utilisateur


= Version 5.1.0 =

Am&eacute;liorations :

* Possibilit&eacute; de ne pas faire apparaitre une modification de la quotation des risques dans les historiques
* Possibilit&eacute; de connaitre le cr&eacute;teur d'une t&acirc;che et d'une action
* Possibilit&eacute; de connaitre le soldeur (personne qui dit que la t&acirc;che ou action est termin&eacute;e) d'une t&acirc;che et d'une action
* Possibilit&eacute; de connaitre la date ou une t&acirc;che et/ou une action ont &eacute;t&eacute; sold&eacute;es
* Possibilit&eacute; d'affecter un utilisateur comme responsable d'une t&acirc;che et d'une action
* Possibilit&eacute; d'affecter un ou plusieurs utilisateurs comme acteurs d'une t&acirc;che et d'une action
* Mise en place de l'option responsable de t&acirc;che et d'action obligatoire
* Ajout d'une interface de gestion pour les options du logiciel
* Mise en place d'un slider pour l'avancement d'une action
* Mise en place de la box qui permet de savoir &agrave; quel &eacute;l&eacute;ment est affect&eacute; une t&acirc;che dans l'interface des actions correctives
* Possibilit&eacute; d'affecter une t&acirc;che &agrave; un groupement, une unit&eacute; de travail ou &agrave; un risque
* Mise en place de l'interface contenant les diff&eacute;rentes actions correctives li&eacute;es &agrave; un risque lors de la modification de ce dernier
* Ajout du statut "Done" (sold&eacute;) et "DoneByChief" (sold&eacute; par le sup&eacute;rieur) dans les t&acirc;ches et actions
* Lors du passage &agrave; 100 pour la progression d'une action on met &agrave; jour le statut de l'action &agrave; "Done"
* Ajout d'options pour laisser la possiblit&eacute; de modifier une tache ou une action m&ecirc;me si celle ci et d&eacute;j&agrave; sold&eacute;e
* Possibilit&eacute; de solder une t&acirc;che sans que tous ses sous-&eacute;l&eacute;ments soient sold&eacute; (On solde tous ses sous-&eacute;l&eacute;ments en cascade)
* Possibilit&eacute; d'affecter des utilisateurs &agrave; la r&eacute;alisation d'une t&acirc;che et d'une action
* Liaison d'une t&acirc;che &agrave; un risque lors de sa modification (Option ne pouvoir affecter uniquement les taches sold&eacute;es)
* Mise &agrave; jour de la progression d'une t&acirc;che lors de la modification d'une action
* Mise en place d'un suivi sur les t&acirc;ches et actions.
* Reprise du suivi des actions correctives avec la quotation avant la cr&eacute;ation de l'action correctives et la quotation apr&egrave;s.
* Avertissement qu'il existe des actions correctives non sold&eacute;es lors de la modification d'un risque
* &Eacute;cran taches &agrave; solder parce que toutes les sous-actions sont sold&eacute;es
* &Eacute;cran taches/actions dont la date est d&eacute;pass&eacute;e mais qui ne sont toujours pas sold&eacute;es
* &Eacute;cran taches/actions sold&eacute;es avec les risques a r&eacute;-&eacute;valuer
* Reprise de l'&eacute;chelle de quotation pour rendre la quotation plus objective et pour correspondre &agrave; la norme du fichier odt
* Ajout d'une option d'auto-installation dans le cas o&ugrave; la version utilis&eacute;e est la version executable
* Possibilit&eacute; de supprimer une tache ou une action
* Affichage de la box risque dans l'&eacute;valuation des risques
* Changement du s&eacute;parateur par d&eacute;faut du fichier pour l'import des utilisateurs
* Changement du role par d&eacute;faut pour les utilisateurs import&eacute;s
* Affichage des extensions autoris&eacute;es pour les fichiers d'import d'utilisateurs
* Possibilit&eacute; d'ajouter des photos aux actions correctives et de d&eacute;finir les photos avant et apr&eacute; l'action
* Option actions correctives avanc&eacute;e pour actions correctives simplifi&eacute;e par d&eacute;faut
* D&eacute;placement du menu principal du plugin pour le remonter juste en dessous du Tabelau de bord de Wordpress
* Ajout d'informations sur les champs obligatoire du formulaire des actions correctives
* Ajout d'un lien pour remplir les champs de date des actions correctives avec la date d'aujourd'hui
* Changement de la gestion de la galerie photo
* Onglet de suivi simplifi&eacute;
* Gestion simplifi&eacute;e des photos avant et apr&egrave; les actions correctives dans le mode simple

Corrections :

* Probl&egrave;me d'affichage lors de l'&eacute;dition d'un &eacute;l&eacute;ment sous internet explorer
* Erreur lors de la cr&eacute;ation de la table de liaison entre les risques et les t&acirc;ches des actions correctives
* Nom de la table methode marqu&eacute; "en dur" dans le script d'insertion de donn&eacute;es
* Suppression de la possibilit&eacute; de cr&eacute;er une unit&eacute; de travail avant tout groupement
* Probl&egrave;me lors de la mise &agrave; jour du co&ucirc;t pour les actions
* Division par z&eacute;ro dans le suivi des actions corectives lorsqu'aucune date n'a &eacute;t&eacute; rentr&eacute;e
* Erreurs php affich&eacute;es lors de la cr&eacute;ation du document unique
* Caract&egrave;res sp&eacute;ciaux &agrave; la g&eacute;n&eacute;ration du document unique au format odt
* Changement de la fa&ccedil;on de cr&eacute;er la r&eacute;f&eacute;rence du document unique
* Suppression de l'espace superflu lorsqu'on demande la g&eacute;n&eacute;ration du document unique (espace du aux champs datepicker)
* Accent de "D&eacute;cembre"
* Pictos des fl&eacute;ches qui ne s'affichaient plus &agrave; cause de leur d&eacute;placement
* Bouton "enregistrer" de la box de liaison entre utilisateur et action qui ne s'affichait pas correctement &agrave; cause d'un mauvais flag &agrave; la cr&eacute;ation
* Champs manquants pour les photos avant et apr&egrave;s les actions correctives
* Lors d'une erreur de mise &agrave; jour sur les photos avant et apr&egrave;s les actions correctives le picto n'&eacute;tait pas bon
* Raccourcissement de la barre de progression d'une action corrective

= Version 5.0.3 =

Am&eacute;liorations :

* Import d'utilisateurs en masse
* Affichage des dangers, m&eacute;thodes et explications de la m&eacute;thode plus claire dans la box d'&eacute;valuation des risques

Corrections :

* Corrections de liens cass&eacute;s sous internent explorer pour l'&eacute;valuation des risques

Modifications:

* Masquage de la box "bilan" dans les unit&eacute;s de travail


= Version 5.0.2 =

Am&eacute;liorations :

* G&eacute;n&eacute;ration du document unique au format Open Office .odt
* Possibilit&eacute;s d'ajouter une image explicative pour les m&eacute;thodes d'&eacute;valuation. Qui s'affichera au niveau des slider d'&eacute;valuation
* Historique des documents unique g&eacute;n&eacute;r&eacute;s, avec possibilit&eacute; de les afficher dans le navigateur
* Indicateur de chargement lors du changement d'onglet dans la box bilan de l'&eacute;valutation des risques
* Liens de t&eacute;l&eacute;chargement des fichiers odt g&eacute;n&eacute;r&eacute;s &agrave; droite du formulaire
* Affichage de l'historique des documents unique
* Fa&ccedil;on de g&eacute;n&eacute;rer le nom du document unique dans le formulaire
* R&eacute;cup&eacute;ration du nom du groupement actuel pour remplir le champs nomSociete du document unique

Ergonomie :

* Chargement de la partie &eacute;valuation des risques lors du clic sur une ligne
* Suppression (par masquage) du bouton de lancement d'&eacute;valuation des risques
* Bouton "supprimer" d&eacute;plac&eacute; en fin de ligne dans l'arbre des &eacute;l&eacute;ments
* Suppression du rechargement de la partie gauche lors de l'&eacute;dition d'un &eacute;l&eacute;ment

Corrections :

* Changement des descriptions pour la livraison sur le svn de wordpress
* Harmonisation de la taille sur le picto categorie de danger
* Mise &agrave; jour du curseur sur les liens
* Mise &agrave; jour du lien vers la page du plugin et de la description courte
* Homog&eacute;n&eacute;isation des pictos
* Mise &agrave; des informations affich&eacute;es pour le theme
* Suppression d'une parenth&egrave;se en trop dans le readme.txt
* Si aucun nom est donn&eacute; pour la g&eacute;n&eacute;ration du document unique alors cr&eacute;ation automatique sur la base de YYYYMMDD_documentUnique_NomSociete
* Cr&eacute;ation picto voiture et mise en place picto risques manutention manuelle

Modification structurelle :

* Changement du format de stockage des champs groupes utilisateurs/risques unitaires/risques par unit&eacute;.
* Ajout d'un champs de statut sur les documents uniques

Am&eacute;liorations code :

* Factorisation de certaines m&eacute;thodes (Arborescence des groupements et unit&eacute;s de travail / affichage de la galerie)
* Reprise de la variable globale pour afficher le picto de suppression dans certains scripts
* Ajout de commentaires dans le code



= Version 4.3.4 vers la Version 5.0.1 =

* Premiere version en b&eacute;ta

Dans cette premiere version nous avons int&eacute;gr&eacute; de nombreuses fonctionnalit&eacute;s par rapport &agrave; la version 4.3.4. Tous les fondamentaux ont &eacute;t&eacute; revus :

M&eacute;thode d'&eacute;valuation :

* Le logiciel est capable d'ing&eacute;rer toutes les m&eacute;thodes d'&eacute;valuation des risques et de transcrire le r&eacute;sultat sur une &eacute;chelle de 0 &agrave; 100 avec un pas de 1
* La possibilit&eacute; d'int&eacute;grer vos m&eacute;thodes d'&eacute;valuations (pour les personnes avertis)
* La m&eacute;thode d'&eacute;valuation des risques implant&eacute;e par d&eacute;faut est bas&eacute;e sur la m&eacute;thode de kinney agr&eacute;ment&eacute;e de 2 facteurs suppl&eacute;mentaires


La mise en place des dangers :

* Cette nouvelle version vous permettra de g&eacute;rer tous vos dangers et leur cat&eacute;gories
* Vous retrouverez, la segmentation de l'INRS dans l'ED840

La gestion multi-soci&eacute;t&eacute; ou multi-services :

* Vous pouvez maintenant g&eacute;rer des structures importantes dans le logiciel et imprimer votre document unique &agrave; chaque niveau de votre structure.
Ceci vous permet, par exemple, de g&eacute;rer une holding avec 5 soci&eacute;t&eacute;s et 25 Services et de g&eacute;rer vos unit&eacute;s de travail a l'unit&eacute; ou sous le groupement souhait&eacute; en fonction des rattachments de responsabilit&eacute;s.
Pour les administrations cela vous permettra de calquer vos &eacute;valuations des risques sur l'organigramme et de r&eacute;aliser vos documents d'&eacute;valuations par services.

L'int&eacute;gration &agrave; Wordpress :

* L'int&eacute;gration &agrave; wordpress fait suite de nombreuses demandes de communication des services des gestions des risques. Worpress est aujourd'hui un des logiciels permettant de g&eacute;rer le plus simplement et avec la meilleure fiabilit&eacute; un site internet.

* Ce site pourra etre d&eacute;ploy&eacute; en interne (intranet) et/ou en externe en version h&eacute;berg&eacute;  ce qui permettra de suivre toutes les actions du service pr&eacute;vention. La communication des responsables s&eacute;curit&eacute; sera de fait extremement simplifi&eacute;e.


Voici les 4 changements fondamentaux qui permettent aujourd'hui &agrave; ce logiciel de ce mettre en place sur des infrastructures de plusieurs milliers de personnes tout en am&eacute;liorant la convivialit&eacute; de d&eacute;part pour les petites entreprises.



== Am&eacute;liorations Futures ==

= 5.1.3 =

* Int&eacute;gration des fonctionnalit&eacute;s de base de la version 4.3.4
* Integration de la gestion des articles (produits,dechets...)
* Int&eacute;gration de la gestion des FDS
* V&eacute;rification des pictos pour les EPI
* Int&eacute;gration des modeles de fiches de postes complet et simple pour l'affichage

= 5.2.0 =

* Gestion des utilisateurs
* Droits d'acces utilisateurs
* selections global des utilateurs, implementation de l'ergonomie utilis&eacute; sur gmail (recherche ajax + pop up pour recherches sp&eacute;ciales et multiselection)
* Possibilit&eacute; d'ordonner les arbres (cat&eacute;gorie etc...)
* Fil d'ariane en mode liste
* V&eacute;rification de la coh&eacute;rence des dates dans les historiques

= 5.3.0 =

* Mise en place des audits sur r&eacute;ferentiels
* Societes intervenantes
* Mise en place de la gestion des machines et outils

= X.X.X non planifi&eacute;es =

* Evaluation des Risques

Mettre en place l'enregistrement en odt &agrave; la place de tcpdf dans a veille r&eacute;glementaire

Implantation des m&eacute;thode de calcul des risques chimiques de l'INRS

Mise en place des tableaux de bord

Mise en place des plans de pr&eacute;vention

Mise en place et gestion des permis feux

Mise en place de la Check list pour la r&eacute;alisation du DUER (idem au r&eacute;f&eacute;rentiels)

Import automatique de hierarchie, groupe et utilisateurs

Mise en place des modeles d'unit&eacute;s de travail, avec un heritage des risques et des actions correctives sur les unit&eacute;s de travail bas&eacute; sur les modeles, l'heritage devra se faire au niveau des risques afin de pouvoir constituer des groupements ou unit&eacute;s bas&eacute; sur ces risques modeles


* Gestion documentaire

Am&eacute;lioration des modeles : int&eacute;gration de champs libres afin qu'ils soient "automatiquement" repris dans le modele .odt

* Recensement des dangers :

Machines utilis&eacute;es sur les unit&eacute;s de travail

Mise en place de la gestion de matieres dangeureuses

Mise en place de la creation des BSD

Gestion des BSD


* Gestion de l'Homme

Gestion des accidents de travail

Gestion des enquetes accident

Gestion des formations


== Upgrade Notice ==

* Version 4.3.4 vers la version 5.0.1

Nous n'avons pas pr&eacute;vu de reprise des donn&eacute;es de la plateforme 4.3.4 pour le moment si vous souhaitez plus d'information n'hesitez pas a nous contacter


== Contactez l'auteur ==

dev@evarisk.com