(function() {

    'use strict';

    if (!!window.JCBilasticTitleComponent)
        return;

    window.JCBilasticTitleComponent = function (params) {

        let that = this;

        that.params = params;

        $(document).ready($.proxy(that.init, this));

    };

    window.JCBilasticTitleComponent.prototype = {

        init: function () {

            let that = this;

            $(document).on('mousedown', $.proxy(that.onMouseDownInsideComponent, that));
            $('.header-search-text').on('focus', $.proxy(that.onFocusComponentInput, that));
            $('.header-mobile-search-button').on('click', $.proxy(that.onClickComponentButton, that));

            $('.header-search-text').typeahead({
                maxItem: that.params.maxSearchItem,
                minLength: that.params.minQueryLength,
                dynamic: true,
                delay: 350,
                highlight: false,
                filter: false,
                cancelButton: false,
                mustSelectItem: false,
                display: ['PREVIEW_PICTURE.SRC', 'NAME', 'DETAIL_PAGE_URL', 'SECTION_PAGE_URL', 'TYPE'],
                selector: {
                    container: 'header-search-wrap',
                    result: 'header-search-result',
                    group: 'header-search-result__group',
                    list: 'header-search-result__list',
                    item: 'header-search-result__item clearfix'
                },
                group: {
                    key: "TYPE",
                    template: function (item) {
                        if (item.TYPE === 'S') {
                            return '<span class="header-search-result__group-name">Разделы</span>';
                        } else {
                            return '<span class="header-search-result__group-name">Товары</span>';
                        }
                    }
                },
                href: function(item) {
                    if (item.TYPE === 'S') {
                        return item.SECTION_PAGE_URL;
                    } else {
                        return item.DETAIL_PAGE_URL;
                    }
                },
                template: function(query, item) {
                    if (item.TYPE === 'S') {
                        return ''+
                            '<span class="header-search-result__info">{{NAME}} ({{ELEMENT_CNT}})</span>'+
                            '';
                    } else {
                        let oldPriceBlock = item.OLD_PRICE > 0 ? '<del>' + item.OLD_PRICE_FORMAT + '</del>' : '',
                            bonusBlock = item.BONUS > 0 ? '<span class="product-price-bonus">TZ-бонусов: ' + item.BONUS + '</span>' : '';
                        return '' +
                            '<span class="header-search-result__image" style="background-image: url(\'{{PREVIEW_PICTURE.SRC}}\')"></span>' +
                            '<span class="header-search-result__info">' +
                            '<span class="product-name">{{NAME}}</span>' +
                            '<span class="product-articul">Арт. {{PROPERTIES.CML2_ARTICLE.VALUE}}</span>' +
                            '<span class="product-status{{PRODUCT_STATUS.AVAIL_CLASS}}">' +
                            '{{PRODUCT_STATUS.AVAIL_TEXT}}' +
                            '</span>' +
                            '</span>' +
                            '<span class="header-search-result__price">' +
                            '<span class="product-price">' +
                            '{{PRICE_FORMAT}}' +
                            oldPriceBlock +
                            '</span>' +
                            bonusBlock +
                            '</span>' +
                            '';
                    }

                },
                templateValue: "{{NAME}}",
                source: {
                    search: {
                        data: function () {
                            let deferred = $.Deferred();
                            BX.ajax.runComponentAction('hands:bilastic.title', 'query', {
                                mode: 'ajax',
                                data: {
                                    query: this.query,
                                },
                                signedParameters: that.params.signedParameters
                            }).then(function (response) {
                                if (response.data) {
                                    deferred.resolve(response.data);
                                }
                            });
                            return deferred;
                        }
                    }
                },
                callback: {
                    onClickAfter: function (node, a, item, event) {
                        event.preventDefault;
                        if(item.href.length > 0) {
                            window.location.href = item.href;
                        }
                        $(this.resultContainer).hide();
                    },
                    onLayoutBuiltBefore: function (node, query, result, resultHtmlList) {
                        if (result.length > 0) {
                            $(this.resultContainer).show();
                        } else {
                            $(this.resultContainer).hide();
                            $(this.resultContainer).empty();
                        }
                    },
                    onLayoutBuiltAfter: function(node, query, result) {
                        let form = $(this.container).find('form');
                        if(result.length > 0) {
                            $(this.resultContainer).find('.header-search-result__group').append('' +
                                '<a href="'+$(form).attr('action')+'?q='+query+'" class="header-search-result__show-all">Показать всё</a>'+
                                '');
                        }
                    },
                    onSubmit: function(node, form, item, event) {
                        event.preventDefault();
                        location.href = $(form).attr('action')+'?q='+$(form).find('[name="q"]').val();
                    }
                }
            });

        },

        onFocusComponentInput: function(event) {
            if(!$('.header-search-result').is(':empty')) {
                $('.header-search-result').show();
            }
        },

        onClickComponentButton: function(event) {
            event.preventDefault();
            let searchInput = $(".header-mobile-search-value-text");
            $(searchInput).click().focus();
            if($(".header-mobile-search").hasClass('active') && $(searchInput).val()) {
                $(".header-mobile-search-text form").trigger('submit');
            }
        },

        onMouseDownInsideComponent: function(event) {
            let searchContainer = $('.header-search.header-search-wrap');
            if(!searchContainer.is(event.target) && searchContainer.has(event.target).length === 0){
                searchContainer.find('.header-search-result').hide();
            }
        }

    };

})();
