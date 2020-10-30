define(["jquery", "isotope"], function ($, isotope) {

	Berserk.behaviors.sc_grid_filter = {
		attach: function (context, settings) {

      $(context).parent().find('.brk-shop-grid-filter:not(.rendered)').addClass('rendered').each(function (i) {

				var	$this					= $(this),
						$grid 				= $this.find('.brk-shop-grid-filter__items'),
						$element 			= $this.find('.brk-shop-grid-filter__item'),
						$buttonGroup 	= $this.find('.brk-shop-grid-filter__button'),
						$checked			= $this.find('.checked');

				$element.addClass('gf-index_' + i);

				// init Isotope
        window.addEventListener('load', function () {
          setTimeout(function () {
            $grid.isotope({
              itemSelector: '.gf-index_' + i,
              percentPosition: true
            });
          }, 300);
        });

				// filter items on button click
				$buttonGroup.on( 'click', 'li', function() {
					var filterValue = $(this).data('filter');
					$grid.isotope({
						filter: filterValue
					});
				});

				$checked.trigger('click');

				$buttonGroup.each(function (i, buttonGroup) {
					var $buttonGroup = $(buttonGroup);
					$buttonGroup.on( 'click', 'li', function () {
						$buttonGroup.find(".checked").removeClass('checked');
						$(this).addClass('checked');
					});
				});

			});

		}
	}

});