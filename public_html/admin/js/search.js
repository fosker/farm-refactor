$( document ).ready(function() {
    $(".search-city").on("click", function() {
        var regions = [];
        var cities = [];
        var search = $('.city-query').val();
        if (search != '') {
            var cityArray = $('.cities-to-search .city');
            $(cityArray).each(function(i, el) {
                if (el.innerHTML.indexOf(search) !== -1) {
                    regions.push($($(el).parents()[3]).attr('data-key'));
                    cities.push($(el).attr('data-key'));
                }
            });
            $(cityArray).each(function(i, el) {
                if (el.innerHTML.indexOf(search) !== -1) {
                    $($(el).parents()[1]).css('display', 'block');
                    $($(el).parents()[3]).css('display', 'block');
                    var button = $($(el).parents()[3]).find('.more-cities');
                    if ($(button).text() == '+') {
                        $(button).trigger('click');
                    }
                    var neighbours = $($(el).parents()[1]).siblings();
                    $(neighbours).each(function(j, city) {
                        var el = $(city).find('.city');
                        if ($.inArray($(el).attr('data-key'), cities) == -1) {
                            $(city).css('display', 'none');
                        }
                    });
                    var neighboursRegions = $($(el).parents()[3]).siblings();
                    $(neighboursRegions).each(function(k, region) {
                        if ($.inArray($(region).attr('data-key'), regions) == -1) {
                            $(region).css('display', 'none');
                        }
                    });
                }
            });
        } else {
            $('.cities-to-search .region').css('display', 'block');
            $('.cities-to-search .region .cities').css('display', 'none');
            $('.cities-to-search .region .more-cities').html('+');
            $('.cities-to-search .region .cities .row').css('display', 'block');
        }
    });

    $(".search-pharmacy").on("click", function() {
        var companies = [];
        var pharmacies = [];
        var search = $('.pharmacy-query').val();
        if (search != '') {
            var pharmaciesArray = $('.company-pharmacy-to-search .pharmacy');
            $(pharmaciesArray).each(function(i, el) {
                if (el.innerHTML.indexOf(search) !== -1) {
                    companies.push($($(el).parents()[3]).attr('data-key'));
                    pharmacies.push($(el).attr('data-key'));
                }
            });
            $(pharmaciesArray).each(function(i, el) {
                if (el.innerHTML.indexOf(search) !== -1) {
                    $($(el).parents()[1]).css('display', 'block');
                    $($(el).parents()[3]).css('display', 'block');
                    var button = $($(el).parents()[3]).find('.more-pharmacies');
                    if ($(button).text() == '+') {
                        $(button).trigger('click');
                    }
                    var neighbours = $($(el).parents()[1]).siblings();
                    $(neighbours).each(function(j, pharmacy) {
                        var el = $(pharmacy).find('.pharmacy');
                        if ($.inArray($(el).attr('data-key'), pharmacies) == -1) {
                            $(pharmacy).css('display', 'none');
                        }
                    });
                    var neighboursCompanies = $($(el).parents()[3]).siblings();
                    $(neighboursCompanies).each(function(k, company) {
                        if ($.inArray($(company).attr('data-key'), companies) == -1) {
                            $(company).css('display', 'none');
                        }
                    });
                }
            });
        } else {
            $('.company-pharmacy-to-search .company').css('display', 'block');
            $('.company-pharmacy-to-search .company .pharmacies').css('display', 'none');
            $('.company-pharmacy-to-search .company .more-pharmacies').html('+');
            $('.company-pharmacy-to-search .company .pharmacies .row').css('display', 'block');
        }
    });

    $(".search-company").on("click", function() {
        var companies = [];
        var search = $('.company-query').val();
        if (search != '') {
            var companiesArray = $('.company-pharmacy-to-search .compan');
            $(companiesArray).each(function(i, el) {
                if (el.innerHTML.indexOf(search) !== -1) {
                    companies.push($($(el).parents()[2]).attr('data-key'));
                }
            });
            $(companiesArray).each(function(i, el) {
                if (el.innerHTML.indexOf(search) !== -1) {
                    //$($(el).parents()[1]).css('display', 'block');
                    //$($(el).parents()[3]).css('display', 'block');
                    var neighbours = $($(el).parents()[2]).siblings();
                    $(neighbours).each(function(j, company) {
                        if ($.inArray($(company).attr('data-key'), companies) == -1) {
                            $(company).css('display', 'none');
                        }
                    });
                }
            });
        } else {
            $('.company-pharmacy-to-search .company').css('display', 'block');
        }
    });
});