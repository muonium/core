/*
* @name         : qC.js
* @authors      : Romain Claveau <romain.claveau@protonmail.ch>, ...
* @description  : Méthode globale pour la gestion de l'interaction entre l'utilisateur et le serveur
* @copyright    : (c) QC 2015
*/

var qC =
{
    /*
    * @description : Initilisation globale de l'interface
    * @mustLoad :
    *   - Panneau de gauche : Arborescence des dossiers, Quota
    *   - Header : Informations sur la session, autres informations utiles
    *   - Panneau de droite : Arborescence du dossier courant (dans ce cas "/"), Listage par défaut "atomique"
    */
    init: function()
    {
        /*
        * Suppression des données locales
        */
        sessionStorage.clear();

        /*
        * Récupération de l'arborescence des dossiers du panneau de gauche
        */
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "inc/actions/arborescence_folders.php", true);
        xhr.onreadystatechange = function()
        {
            if(xhr.status == 200 && xhr.readyState == 4)
            {

            }
        }
        xhr.send(null);

        /*
        * Récupération des informations sur le quota
        */
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "inc/actions/quota.php", true);
        xhr.onreadystatechange = function()
        {
            if(xhr.status == 200 && xhr.readyState == 4)
            {
              
            }
        }
        xhr.send(null);

        /*
        * Récupération de l'arborescence courante
        */
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "inc/actions/arborescence_current.php", true);
        xhr.onreadystatechange = function()
        {
            if(xhr.status == 200 && xhr.readyState == 4)
            {

            }
        }
        xhr.send(null);

        /*
        * Chargement des fichiers et dossiers appartenant à l'utilisateur
        */
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "inc/actions/list_elements.php?type=atomic", true); // Listage par défaut : "atomic"
        xhr.onreadystatechange = function()
        {
            if(xhr.status == 200 && xhr.readyState == 4)
            {
                qC.checkActions();
            }
        }
        xhr.send(null);

        sessionStorage.setItem("listage", "atomic");
    },

    /*
    * @description : Permet de sélectionner ou désélectionner un élément
    */
    checkElement: function(element)
    {
        if(element.className.indexOf("selected") == -1)
        {
            element.className += " selected";
        }
        else
        {
            element.className = element.className.replace(" selected", "");
        }

        qC.checkActions();
    },

    /*
    * @description : Permet de charger les actions relatives aux éléments sélectionnés
    *   - Un fichier sélectionné : Ouvrir, Renommer, Supprimer, Copier, Couper, Télécharger, Propriétés, Favoris
    *   - Un dossier sélectionné : Ouvrir, Renommer, Supprimer, Copier, Couper, Zipper, Propriétés, Favoris
    *   - Plusieurs éléments sélectionnés : Supprimer, Copier, Couper, Zipper
    *   - Rien n'est sélectionné : Créer un fichier, créer un dossier, uploader, coller (si présence d'une instance copier/couper)
    */
    checkActions: function()
    {
        /*
        * Récupération des éléments sélectionnés
        */
        var elements = document.querySelectorAll("#panel_right_listingElements .selected");
        var container = document.querySelector("#panel_right_listingElements .actions_parentFolder");

        /*
        * MàZ des actions
        */
        container.innerHTML = "";

        /*
        * Traitement cas par cas, voir @description
        */
        if(elements.length == 0) // Pas d'élément
        {
            container.innerHTML =
                "<div class='actions_atom element_root_folder_pos1' onclick='qC.actions.openPopUp(\"createFile\")'>" +
                    "<p><img src='images/actions/create_file.svg' /></p>" +
                "</div>" +
                "<div class='actions_atom element_root_folder_pos3' onclick='qC.actions.openPopUp(\"createFolder\")'>" +
                    "<p><img src='images/actions/create_folder.svg' /></p>" +
                "</div>" +
                "<div class='actions_atom element_root_folder_pos5' onclick='qC.actions.openPopUp(\"upload\");'>" +
                    "<p><img src='images/actions/upload.svg' /></p>" +
                "</div>" +
                "<div class='actions_atom element_root_folder_pos7' onclick='qC.actions.paste();'>" +
                    "<p><img src='images/actions/paste.svg' /></p>" +
                "</div>";
        }
        else if(elements.length == 1) // 1 élément
        {
            if(elements[0].getAttribute("data-type") == "folder") // 1 dossier
            {
                container.innerHTML =
                    "<div class='actions_atom element_root_folder_pos1' onclick='qC.actions.open();'>" +
                        "<p><img src='images/actions/view.svg' /></p>" +
                    "</div>" +
                    "<div class='actions_atom element_root_folder_pos2' onclick='qC.actions.rename();'>" +
                        "<p><img src='images/actions/rename.svg' /></p>" +
                    "</div>" +
                    "<div class='actions_atom element_root_folder_pos3' onclick='qC.actions.trash();'>" +
                        "<p><img src='images/actions/trash.svg' /></p>" +
                    "</div>" +
                    "<div class='actions_atom element_root_folder_pos4' onclick='qC.actions.copy();'>" +
                        "<p><img src='images/actions/copy.svg' /></p>" +
                    "</div>" +
                    "<div class='actions_atom element_root_folder_pos5' onclick='qC.actions.cut();'>" +
                        "<p><img src='images/actions/cut.svg' /></p>" +
                    "</div>" +
                    "<div class='actions_atom element_root_folder_pos6' onclick='qC.actions.zip();'>" +
                        "<p><img src='images/actions/zip.svg' /></p>" +
                    "</div>" +
                    "<div class='actions_atom element_root_folder_pos7' onclick='qC.actions.properties();'>" +
                        "<p><img src='images/actions/properties.svg' /></p>" +
                    "</div>" +
                    "<div class='actions_atom element_root_folder_pos8' onclick='qC.actions.favorites();'>" +
                        "<p><img src='images/actions/putInFavorites.svg' /></p>" +
                    "</div>";
            }
            else // 1 fichier
            {
                container.innerHTML =
                    "<div class='actions_atom element_root_folder_pos1' onclick='qC.actions.open();'>" +
                        "<p><img src='images/actions/view.svg' /></p>" +
                    "</div>" +
                    "<div class='actions_atom element_root_folder_pos2' onclick='qC.actions.rename();'>" +
                        "<p><img src='images/actions/rename.svg' /></p>" +
                    "</div>" +
                    "<div class='actions_atom element_root_folder_pos3' onclick='qC.actions.trash();'>" +
                        "<p><img src='images/actions/trash.svg' /></p>" +
                    "</div>" +
                    "<div class='actions_atom element_root_folder_pos4' onclick='qC.actions.copy();'>" +
                        "<p><img src='images/actions/copy.svg' /></p>" +
                    "</div>" +
                    "<div class='actions_atom element_root_folder_pos5' onclick='qC.actions.cut();'>" +
                        "<p><img src='images/actions/cut.svg' /></p>" +
                    "</div>" +
                    "<div class='actions_atom element_root_folder_pos6' onclick='qC.actions.download();'>" +
                        "<p><img src='images/actions/download.svg' /></p>" +
                    "</div>" +
                    "<div class='actions_atom element_root_folder_pos7' onclick='qC.actions.properties();'>" +
                        "<p><img src='images/actions/properties.svg' /></p>" +
                    "</div>" +
                    "<div class='actions_atom element_root_folder_pos8' onclick='qC.actions.favorites();'>" +
                        "<p><img src='images/actions/putInFavorites.svg' /></p>" +
                    "</div>";
            }
        }
        else // Plusieurs éléments
        {
            container.innerHTML =
                "<div class='actions_atom element_root_folder_pos1' onclick='qC.actions.trash();'>" +
                    "<p><img src='images/actions/trash.svg' /></p>" +
                "</div>" +
                "<div class='actions_atom element_root_folder_pos3' onclick='qC.actions.copy();'>" +
                    "<p><img src='images/actions/copy.svg' /></p>" +
                "</div>" +
                "<div class='actions_atom element_root_folder_pos5' onclick='qC.actions.cut();'>" +
                    "<p><img src='images/actions/cut.svg' /></p>" +
                "</div>" +
                "<div class='actions_atom element_root_folder_pos7' onclick='qC.actions.zip();'>" +
                    "<p><img src='images/actions/zip.svg' /></p>" +
                "</div>";
        }
    }
};
