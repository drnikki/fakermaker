/**
 * @file
 * Block behaviors.
 */

(function ($, window, Drupal) {

  'use strict';
// TODO: make this better
    Drupal.behaviors.fakermakerHideWeight = {
      attach: function (context, settings) {
        // tableDrag is required and we should be on the fakermakers admin page.
        if (typeof Drupal.tableDrag === 'undefined' || typeof Drupal.tableDrag.fakermaker === 'undefined') {
          return;
        }

        $('#fakermaker')
          .find('thead > tr > th:nth-child(5)').addClass('tabledrag-hide').css('display', 'none');
        $('#fakermaker')
          .find('tbody > tr > td:nth-child(5)').addClass('tabledrag-hide').css('display', 'none');

      }
    }

  /**
   * Move a row in the settings table between regions via select list.
   *
   * This behavior is dependent on the tableDrag behavior, since it uses the
   * objects initialized in that behavior to update the row.
   *
   * @type {Drupal~behavior}
   *
   * @prop {Drupal~behaviorAttach} attach
   *   Attaches the tableDrag behaviour for items in fakermaker administration.
   */
  Drupal.behaviors.fakermakerDrag = {
    attach: function (context, settings) {
      // tableDrag is required and we should be on the fakermakers admin page.
      if (typeof Drupal.tableDrag === 'undefined' || typeof Drupal.tableDrag.fakermaker === 'undefined') {
        return;
      }

      /**
       * Function to check empty regions and toggle classes based on this.
       *
       * @param {jQuery} table
       *   The jQuery object representing the table to inspect.
       * @param {jQuery} rowObject
       *   The jQuery object representing the table row.
       */
      function checkEmptyRegions(table, rowObject) {
        table.find('tr.region-message').each(function () {
          var $this = $(this);
          // If the dragged row is in this region, but above the message row,
          // swap it down one space.
          if ($this.prev('tr').get(0) === rowObject.element) {
            // Prevent a recursion problem when using the keyboard to move rows
            // up.
            if ((rowObject.method !== 'keyboard' || rowObject.direction === 'down')) {
              rowObject.swap('after', this);
            }
          }
          // This region has become empty.
          if ($this.next('tr').is(':not(.draggable)') || $this.next('tr').length === 0) {
            $this.removeClass('region-populated').addClass('region-empty');
          }
          // This region has become populated.
          else if ($this.is('.region-empty')) {
            $this.removeClass('region-empty').addClass('region-populated');
          }
        });
      }

      /**
       * Function to update the last placed row with the correct classes.
       *
       * @param {jQuery} table
       *   The jQuery object representing the table to inspect.
       * @param {jQuery} rowObject
       *   The jQuery object representing the table row.
       */
      function updateLastPlaced(table, rowObject) {
        // Remove the color-success class from new item if applicable.
        table.find('.color-success').removeClass('color-success');

        var $rowObject = $(rowObject);
        if (!$rowObject.is('.drag-previous')) {
          table.find('.drag-previous').removeClass('drag-previous');
          $rowObject.addClass('drag-previous');
        }
      }

      /**
       * Update item weights in the given region.
       *
       * @param {jQuery} table
       *   Table with draggable items.
       * @param {string} region
       *   Machine name of region containing items to update.
       */
      function updateItemWeights(table, region) {
        // Calculate minimum weight.
        var weight = -Math.round(table.find('.draggable').length / 2);
        // Update the item weights.
        table.find('.region-' + region + '-message').nextUntil('.region-title')
          .find('select.fakermaker-weight').val(function () {
            return ++weight;
          });
      }

      var table = $('#fakermaker');
      // Get the items tableDrag object.
      var tableDrag = Drupal.tableDrag.fakermaker;
      // Add a handler for when a row is swapped, update empty regions.
      tableDrag.row.prototype.onSwap = function (swappedRow) {
        checkEmptyRegions(table, this);
        updateLastPlaced(table, this);
      };

      // Add a handler so when a row is dropped, update fields dropped into
      // new regions.
      tableDrag.onDrop = function () {
        var dragObject = this;
        var $rowElement = $(dragObject.rowObject.element);
        // Use "region-message" row instead of "region" row because
        // "region-{region_name}-message" is less prone to regexp match errors.
        var regionRow = $rowElement.prevAll('tr.region-message').get(0);
        var regionName = regionRow.className.replace(/([^ ]+[ ]+)*region-([^ ]+)-message([ ]+[^ ]+)*/, '$2');
        var regionField = $rowElement.find('select.fakermaker-status-select');
        // Check whether the newly picked region is available for this item.
        if (regionField.find('option[value=' + regionName + ']').length === 0) {
          // If not, alert the user and keep the item in its old region
          // setting.
          window.alert(Drupal.t('The item cannot be placed in this region.'));
          // Simulate that there was a selected element change, so the row is
          // put back to from where the user tried to drag it.
          regionField.trigger('change');
        }

        // Update region and weight fields if the region has been changed.
        if (!regionField.is('.fakermaker-status-' + regionName)) {
          var weightField = $rowElement.find('select.fakermaker-weight');
          var oldRegionName = weightField[0].className.replace(/([^ ]+[ ]+)*fakermaker-weight-([^ ]+)([ ]+[^ ]+)*/, '$2');
          regionField.removeClass('fakermaker-status-' + oldRegionName).addClass('fakermaker-status-' + regionName);
          weightField.removeClass('fakermaker-weight-' + oldRegionName).addClass('fakermaker-weight-' + regionName);
          regionField.val(regionName);
        }

        updateItemWeights(table, regionName);
      };

      // Add the behavior to each status select list.
      $(context).find('select.fakermaker-status-select').once('fakermaker-status-select')
        .on('change', function (event) {
          // Make our new row and select field.
          var row = $(this).closest('tr');
          var select = $(this);
          // Find the correct region and insert the row as the last in the region.
          tableDrag.rowObject = new tableDrag.row(row[0]);
          var region_message = table.find('.region-' + select[0].value + '-message');
          var region_items = region_message.nextUntil('.region-message, .region-title');
          if (region_items.length) {
            region_items.last().after(row);
          }
          // We found that region_message is the last row.
          else {
            region_message.after(row);
          }
          updateItemWeights(table, select[0].value);
          // Modify empty regions with added or removed fields.
          checkEmptyRegions(table, tableDrag.rowObject);
          // Update last placed fakermaker indication.
          updateLastPlaced(table, row);
          // Show unsaved changes warning.
          if (!tableDrag.changed) {
            $(Drupal.theme('tableDragChangedWarning')).insertBefore(tableDrag.table).hide().fadeIn('slow');
            tableDrag.changed = true;
          }
          // Remove focus from selectbox.
          select.trigger('blur');
        });
    }
  };

})(jQuery, window, Drupal);
