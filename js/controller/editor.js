/**
 * Manage all custom editor component
 *
 * @package editor
 * @copyright (C) 2014 jdRoll
 * @license MIT
 */
var editorControllerImpl = function() {
  var srv = {};

  srv.activatePRVTag = activatePRVTag;
  srv.activatePNJTag = activatePNJTag;
  srv.activateCarteTag = activateCarteTag;

  function activatePNJTag() {

    $('select').select2()
      .on("select2-close", function() {
        $('select').select2("open");
      });
    $('select').select2().select2({
      containerCssClass: "Select2ContainerEditor",
      dropdownCssClass: "Select2DropDownEditor"
    }).select2("open");
  }

  function activateCarteTag() {

    $('select').select2()
      .on("select2-close", function() {
        $('select').select2("open");
      });
    $('select').select2().select2({
      containerCssClass: "Select2ContainerEditor",
      dropdownCssClass: "Select2DropDownEditor"
    }).select2("open");
  }

  function activatePRVTag() {
    $('.multiselect').trigger('show');
    $('.multiselect').multiselect({
      enableCaseInsensitiveFiltering: true,
      maxHeight: 150,
      numberDisplayed: 1,
      nonSelectedText: "Sélection des personnages",
      buttonWidth: 290,
      onDropdownHide: function(e) {
        e.preventDefault();
      },
      onDropdownShow: function() {

      }
    });
    $('.multiselect').click();
  }

  return srv;
};

var editorController = editorControllerImpl();
