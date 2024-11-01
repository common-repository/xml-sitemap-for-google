jQuery(document).ready(function ($) {

    initializieDateTimePicker();

    var plugin_url = url.plugin_url;
    var premium_access = url.premium_access;
    var pop_up_box = document.getElementById("pop-up-box");
    var rename_sitemap_url = document.getElementById("rename_sitemap_url_th");
    var links_per_sitemap_th = document.getElementById("links_per_sitemap_th");
    var enable_additional_pages_th = document.getElementById("enable_additional_pages_th");

    if(premium_access == 1 && rename_sitemap_url){
        rename_sitemap_url.style.paddingTop = '18px';
        links_per_sitemap_th.style.paddingTop = '14px';
        enable_additional_pages_th.style.paddingTop = '18px';
    }

    $('.tab-content').not('#general').hide();
    if(pop_up_box){
        pop_up_box.style.display = "none";
    }
    $('.nav-tab-wrapper a').click(function () {
        var tab_id = $(this).attr('href');
        $('.nav-tab-wrapper a').removeClass('nav-tab-active');
        $('.tab-content').hide();
        $(this).addClass('nav-tab-active');
        $(tab_id).show();
        return false;
    });

    // Add New Row
    $('#add-new-page').click(function () {
        $('#additional-pages-table tbody').append(`
                <tr class="additional-page-row">
                        <td>
                            <input type="checkbox" class="delete-checkbox">
                        </td>
                        <td><input style="width:100%; text-align:center;" type="text" name="additional_pages[url][]" class="additional-url"></td>
                        <td>
                            <select name="additional_pages[priority][]">
                                <option value="0">0</option>
                                <option value="0.1">0.1</option>
                                <option value="0.2">0.2</option>
                                <option value="0.3">0.3</option>
                                <option value="0.4">0.4</option>
                                <option value="0.5">0.5</option>
                                <option value="0.6">0.6</option>
                                <option value="0.7">0.7</option>
                                <option value="0.8">0.8</option>
                                <option value="0.9">0.9</option>
                                <option value="1">1</option>
                            </select>
                        </td>
                        <td>
                            <select name="additional_pages[frequency][]">
                                <option value="always">Always</option>
                                <option value="hourly">Hourly</option>
                                <option value="daily">Daily</option>
                                <option value="weekly">Weekly</option>
                                <option value="monthly">Monthly</option>
                                <option value="yearly">Yearly</option>
                                <option value="never">Never</option>
                            </select>
                        </td>
                        
                        <td><input type="text" name="additional_pages[last_modified][]" class="additional-last-modified" readonly value=""></td>
                        </td>
                        <td><button type="button" class="button button-secondary delete-row" id='delete-row'><img src="` + plugin_url + `admin/assets/images/delete.png" alt="delete" title="Delete"/>
                        </button></td>
                </tr>
            `);

        // Set placeholder with current UTC time in the format "YYYY-Mon-DD HH:mm"
        var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        var currentDate = new Date();
        var utcDate = new Date(Date.UTC(
            currentDate.getUTCFullYear(),
            currentDate.getUTCMonth(),
            currentDate.getUTCDate(),
            currentDate.getUTCHours(),
            currentDate.getUTCMinutes(),
            currentDate.getUTCSeconds()
        ));
        var formattedDate = utcDate.getUTCFullYear() + '-' + months[utcDate.getUTCMonth()] + '-' + ('0' + utcDate.getUTCDate()).slice(-2) + ' ' + ('0' + utcDate.getUTCHours()).slice(-2) + ':' + ('0' + utcDate.getUTCMinutes()).slice(-2);
        $('.additional-last-modified').last().attr('placeholder', formattedDate);

        initializieDateTimePicker();
    });

    // Delete Row
    $('#additional-pages-table').on('click', '.delete-row', function () {
        $(this).closest('.additional-page-row').remove();
    });

    var selectElement = document.querySelector('.bulk-actions');
    if (selectElement != null) {
        selectElement.addEventListener('change', function () {
            var selectedValue = selectElement.value;
            $('.apply-btn').click(function () {
                if (selectedValue == 'delete-selected') {
                    $('.delete-checkbox:checked').each(function () {
                        $(this).closest('.additional-page-row').remove();
                        $('#check-all').prop('checked', false);
                    });
                }
            });
        });
    }

    // Check All
    $('#additional-pages-table').on('change', '#check-all', function () {
        if ($(this).is(':checked')) {
            $('.delete-checkbox').prop('checked', true);
        } else {
            $('.delete-checkbox').prop('checked', false);
        }
    });

    $('#xmlsbw_include_all_post_type').change(function () {
        if ($(this).is(':checked')) {
            $('.post-type-class').prop('checked', true).prop('disabled', true);
            $('.taxonomy-type-class').prop('checked', true).prop('disabled', true);
        } else {
            $('.post-type-class').prop('checked', false).prop('disabled', false);
            $('.taxonomy-type-class').prop('checked', false).prop('disabled', false);
        }
    });

    $('#xmlsbw_include_all_post_type_html').change(function () {
        if ($(this).is(':checked')) {
            $('.post-type-html-class').prop('checked', true).prop('disabled', true);
            $('.taxonomy-type-html-class').prop('checked', true).prop('disabled', true);
        } else {
            $('.post-type-html-class').prop('checked', false).prop('disabled', false);
            $('.taxonomy-type-html-class').prop('checked', false).prop('disabled', false);
        }
    });

    function initializieDateTimePicker() {
        $(document).on('focus', '.additional-last-modified', function () {
            $(this).datetimepicker({
                dateFormat: 'yy-M-dd',
                timeFormat: 'HH:mm',
                showOtherMonths: true,
                selectOtherMonths: true,
                changeMonth: true,
                changeYear: true,
                currentText: 'Today',
                maxDate: 0,
                maxTime: 0,
                onSelect: function (dateText) {
                    $(this).val(dateText);
                }
            });

            $.datepicker._gotoToday = function (id) {
                var target = $(id);
                var inst = this._getInst(target[0]);
                var date = new Date();
                var utcDate = new Date(Date.UTC(date.getUTCFullYear(), date.getUTCMonth(), date.getUTCDate(),
                    date.getUTCHours(), date.getUTCMinutes(), date.getUTCSeconds()));
                this._setDate(inst, utcDate);
                this._hideDatepicker();
            };
        });
    }

    $('#html_tab').click(function () {
        if(premium_access == 0){
            pop_up_box.style.display = "block";
            document.getElementById("submit-button").style.filter = "blur(3px)";
            document.getElementById("submit-button").style.pointerEvents = "none";
        }
    });
    $('#xml_tab').click(function () {
        pop_up_box.style.display = "none"; 
        document.getElementById("submit-button").style.filter = "none";
        document.getElementById("submit-button").style.pointerEvents = "auto";
    });

    function initializeAutocomplete(inputSelector, containerSelector, dataAction,item1,item2,item3) {

        $(document).on('click', '.' + item2, function () {
            $(this).closest('.' + item1).remove();
        });

        $(inputSelector).autocomplete({
            source: function (request, response) {
                $.ajax({
                    url: ajaxurl,
                    dataType: 'json',
                    data: {
                        action: dataAction,
                        term: request.term
                    },
                    success: function (data) {
                        response(data);
                    }
                });
            },
            select: function (event, ui) {
                $(containerSelector).append('<div class="' + item1 +'"><span>' + ui.item.label + '</span><input type="hidden" name="' + item3 + '[]" value="' + ui.item.value + '"><button type="button" class="'+ item2 +'">X</button></div>');
                $(this).val('');
                return false;
            }
        });
    }

    initializeAutocomplete('#excluded_posts_input', '#excluded_posts_container', 'search_posts','excluded-post','remove-excluded-post','xmlsbw_excluded_posts');
    initializeAutocomplete('#excluded_posts_html_input', '#excluded_posts_html_container', 'search_posts','excluded-post-html','remove-excluded-post-html','xmlsbw_excluded_posts_html');
    initializeAutocomplete('#excluded_terms_input', '#excluded_terms_container', 'search_terms','excluded-term','remove-excluded-term','xmlsbw_excluded_terms');
    initializeAutocomplete('#excluded_terms_html_input', '#excluded_terms_html_container', 'search_terms','excluded-term-html','remove-excluded-term-html','xmlsbw_excluded_terms_html');

    $('#select_posts_input').autocomplete({
        source: function (request, response) {
            $.ajax({
                url: ajaxurl,
                dataType: 'json',
                data: {
                    action: 'search_posts_pages',
                    term: request.term
                },
                success: function (data) {
                    response(data);
                }
            });
        },
        select: function (event, ui) {
            $('#select_posts_input').val(ui.item.label);
            $('#selected_post_permalink').val(ui.item.permalink);
            return false;
        }
    });

    const dropdown = document.getElementById('xmlsbw_saved_keyword');
    const linkPlaceholder = document.getElementById('dynamic-link');
    const copyButton = document.getElementById('copy-button');

    function updateLink() {
        const selectedValue = dropdown?.value;
        if (selectedValue) {
            let parts = selectedValue?.split('|');
            let trimmedValue = parts[0];
            const selectedText = dropdown?.options[dropdown.selectedIndex].text;
            if (linkPlaceholder) {
                linkPlaceholder.textContent = '<a href="' + trimmedValue + '">' + selectedText + '</a>';
            }
        }
    }

    function copyLink() {
        event.preventDefault();
        const range = document.createRange();
        range.selectNode(linkPlaceholder);
        window.getSelection().removeAllRanges();
        window.getSelection().addRange(range);
        document.execCommand('copy');
        window.getSelection().removeAllRanges();
        
    }

    updateLink();
    if (dropdown) {
        dropdown.addEventListener('change', updateLink);
    }
    if (copyButton) {
        copyButton.addEventListener('click', copyLink);
    }

    $('#copy-shortcode').click(function () {
        var text = "[show_html_sitemap]";
        var tempInput = document.createElement("textarea");
        tempInput.value = text;
        document.body.appendChild(tempInput);
        tempInput.select();
        tempInput.setSelectionRange(0, 99999);
        document.execCommand("copy");
        document.body.removeChild(tempInput);

        var svg = document.querySelector('.copy-shortcode svg');
        var originalSVG = svg.innerHTML;
        svg.innerHTML = `<svg clip-rule="evenodd" fill-rule="evenodd" image-rendering="optimizeQuality" shape-rendering="geometricPrecision" text-rendering="geometricPrecision" 
                            viewBox="0 0 2.54 2.54" xmlns="http://www.w3.org/2000/svg" id="fi_14025310"><g id="图层_x0020_1">
                            <circle cx="1.27" cy="1.27" fill="#48b02c" r="1.27"></circle><g fill="#fff">
                            <path d="m.96229 1.62644.8951-.89509c.02637-.02638.06967-.02611.09578 0l.08642.08642c.02611.02611.02611.06968 0 .09578l-.89509.8951c-.02611.02611-.06941.02638-.09579 0l-.08642-.08642c-.02638-.02638-.02638-.06941 0-.09579z"></path>
                            <path d="m.6827 1.08089.54525.54525c.02637.02638.02606.06973 0 .09579l-.08642.08642c-.02606.02605-.06973.02605-.09579 0l-.54525-.54525c-.02606-.02606-.02637-.06941 0-.09579l.08642-.08642c.02638-.02637.06941-.02637.09579 0z"></path>
                            </g></g></svg>`;
        setTimeout(function() {
            svg.innerHTML = originalSVG;
        }, 3000);
    });

    $('#copy-phpcode').click(function () {
        var text = "<?php if( function_exists( 'show_html_sitemap' ) ) show_html_sitemap(); ?>";
        var tempInput = document.createElement("textarea");
        tempInput.value = text;
        document.body.appendChild(tempInput);
        tempInput.select();
        tempInput.setSelectionRange(0, 99999);
        document.execCommand("copy");
        document.body.removeChild(tempInput);

        var svg = document.querySelector('.copy-phpcode svg');
        var originalSVG = svg.innerHTML;
        svg.innerHTML = `<svg clip-rule="evenodd" fill-rule="evenodd" image-rendering="optimizeQuality" shape-rendering="geometricPrecision" text-rendering="geometricPrecision" 
                            viewBox="0 0 2.54 2.54" xmlns="http://www.w3.org/2000/svg" id="fi_14025310"><g id="图层_x0020_1">
                            <circle cx="1.27" cy="1.27" fill="#48b02c" r="1.27"></circle><g fill="#fff">
                            <path d="m.96229 1.62644.8951-.89509c.02637-.02638.06967-.02611.09578 0l.08642.08642c.02611.02611.02611.06968 0 .09578l-.89509.8951c-.02611.02611-.06941.02638-.09579 0l-.08642-.08642c-.02638-.02638-.02638-.06941 0-.09579z"></path>
                            <path d="m.6827 1.08089.54525.54525c.02637.02638.02606.06973 0 .09579l-.08642.08642c-.02606.02605-.06973.02605-.09579 0l-.54525-.54525c-.02606-.02606-.02637-.06941 0-.09579l.08642-.08642c.02638-.02637.06941-.02637.09579 0z"></path>
                            </g></g></svg>`;
        setTimeout(function() {
            svg.innerHTML = originalSVG;
        }, 3000);
    });

    $('#copy-button').click(function () {
        var svg = document.querySelector('.copy-btn svg');
        var originalSVG = svg.innerHTML;
        svg.innerHTML = `<svg clip-rule="evenodd" fill-rule="evenodd" image-rendering="optimizeQuality" shape-rendering="geometricPrecision" text-rendering="geometricPrecision" 
                            viewBox="0 0 2.54 2.54" xmlns="http://www.w3.org/2000/svg" id="fi_14025310"><g id="图层_x0020_1">
                            <circle cx="1.27" cy="1.27" fill="#48b02c" r="1.27"></circle><g fill="#fff">
                            <path d="m.96229 1.62644.8951-.89509c.02637-.02638.06967-.02611.09578 0l.08642.08642c.02611.02611.02611.06968 0 .09578l-.89509.8951c-.02611.02611-.06941.02638-.09579 0l-.08642-.08642c-.02638-.02638-.02638-.06941 0-.09579z"></path>
                            <path d="m.6827 1.08089.54525.54525c.02637.02638.02606.06973 0 .09579l-.08642.08642c-.02606.02605-.06973.02605-.09579 0l-.54525-.54525c-.02606-.02606-.02637-.06941 0-.09579l.08642-.08642c.02638-.02637.06941-.02637.09579 0z"></path>
                            </g></g></svg>`;
        setTimeout(function() {
            svg.innerHTML = originalSVG;
        }, 3000);
    });

    var homeUrl = url.home_url;

    $('#xmlsbw-sitemap-settings-form').submit(function (event) {
        event.preventDefault();

        let urls = [];
        let allFieldsNotEmpty = true;
        let duplicateUrl = false;
        
        var xmlsbw_links_per_sitemap = document.getElementById('xmlsbw_links_per_sitemap');
        var checkbox = document.getElementById('xmlsbw_enable_sitemap_generation');
        var isChecked = checkbox.checked;
        if (xmlsbw_links_per_sitemap) {
            var linksPerSitemapValue = xmlsbw_links_per_sitemap.value;
        
            if (linksPerSitemapValue === "" && isChecked == true) {
                alert("Links per sitemap cannot be empty.");
                return;
            }

            if (linksPerSitemapValue == 0 && isChecked == true) {
                alert("Links per sitemap cannot be 0.");
                return;
            }
    
            if ((isNaN(linksPerSitemapValue) || linksPerSitemapValue <= 0) && isChecked == true) {
                alert("Links per sitemap must be a positive number.");
                return;
            }

        }
        

        var xmlsbw_sitemap_url = document.getElementById('xmlsbw_sitemap_url').value;
        if (!/^(?=.*[a-zA-Z].*[a-zA-Z])[a-zA-Z0-9-_]{4,}$/.test(xmlsbw_sitemap_url) && isChecked == true) {
            alert("Sitemap URL must contain at least 4 characters consisting of alphabets, numeric values, and hyphens.");
            return;
        }

        $('#additional-pages-table tbody tr').slice(1).each(function () {
            let url = $(this).find('.additional-url').val();
            if (urls.includes(url)) {
                duplicateUrl = true;
                return false;
            }
            urls.push(url);
            if (url.trim() === '') {
                allFieldsNotEmpty = false;
                return false;
            }
        });

        if (duplicateUrl) {
            alert("URL already exists.");
            return;
        }

        if (!allFieldsNotEmpty) {
            alert("Please enter a valid URL.");
            return;
        }

        if (urls.every(url => url.includes(homeUrl))) {
            var formData = $(this).serialize();
            $.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {
                    action: 'xmlsbw_save_sitemap_settings',
                    formData: formData
                },
                beforeSend: function () {
                    $('.loader').css('display', 'block');
                },
                success: function (response) {
                    $('.loader').css('display', 'none');
                    if(response.success){
                        $('.success-message').html("Settings Updated Successfully").css({ 'display': 'block', 'color': 'green' });
                    }
                    else{
                        $('.success-message').html("").css({ 'display': 'none' });
                        alert("Please ensure you select at least one post type or taxonomy type for display in the HTML Sitemap.");
                    }
                },
                error: function (xhr, status, error) {
                    $('.loader').css('display', 'none');
                }
            });
        } else {
            alert("Make sure the URL contains the domain name.");
        }
    });

    $('.search-box').on('input', function () {
        var searchText = $(this).val().toLowerCase();
        if (searchText.trim() === '') {
            $('.additional-page-row').show();
            $('.additional-url').each(function () {
                var url = $(this).val().toLowerCase();
                if (url == "") {
                    $(this).closest('.additional-page-row').hide();
                }
            });
        } else {
            $('.additional-page-row').hide();
            $('.additional-url').each(function () {
                var url = $(this).val().toLowerCase();
                if (url.includes(searchText)) {
                    $(this).closest('.additional-page-row').show();
                }
            });
        }
    });

    var pop_up_box_upgrade = document.getElementById("pop-up-box-upgrade");
    const elementsToBlur = ["content","xml-pricing-cards","content-inside","select-page-div","validate-btn","xml-plans-p","xml-heading","right-box-xmlsbw"];
    if(pop_up_box_upgrade){
        pop_up_box_upgrade.style.display = "none";
    }

    $('.close-popup').click(function () {
        event.preventDefault();
        pop_up_box_upgrade.style.display = "none";
        elementsToBlur.forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                element.style.filter = "none";
                element.style.pointerEvents = "auto";
            }
        });
    });

    $('#xmlsbw-upgrade-to-premium').submit(function (event) {
        event.preventDefault();
        var inputValue = $('#select_posts_input').val().trim();
        if (inputValue != '') {
            var page_url = document.getElementById('selected_post_permalink').value;
			var page_name = document.getElementById('select_posts_input').value;
			var keyword_value = document.getElementById('keyword_value').value;
            var formData = $(this).serialize();
            $.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {
                    action: 'xmlsbw_save_upgrade_option',
                    formData: formData,
                    title: inputValue
                },
                beforeSend: function () {
                    $('.loader').css('display', 'block');
                },
                success: function (response) {
                    $('.loader').css('display', 'none');
                    if(response.data.valid){
                        if (response.success) {
                            $('#xml-heading').text('Pro Access Enabled').css('color','#4AB01A');
                            $('.content').css('border-top', '3px solid #4AB01A');
                            $('.content .content-inside1 .xml-right-des ul li').css('background-color', 'white');
                            $('.content .content-inside1 .xml-right-des ul li').css('border', 'border: 0.5px solid #4AB01A');
                            var svg = document.querySelectorAll('.content .content-inside1 .xml-right-des ul li svg');
                            svg.forEach(function(svgElement) {
                                svgElement.innerHTML = `<g mask="url(#mask0_4_9)">
                                                <path d="M16.5869 7.49821L15.4275 6.33898C15.1523 6.06381 14.9268 5.52001 14.9268 5.13004V3.4906C14.9268 2.71072 14.2897 2.07363 13.51 2.07321H11.8699C11.4804 2.07321 10.936 1.8473 10.6608 1.57233L9.50158 0.413107C8.95077 -0.137702 8.04892 -0.137702 7.49811 0.413107L6.33888 1.57316C6.06346 1.84834 5.51842 2.07363 5.12973 2.07363H3.49029C2.71145 2.07363 2.07353 2.71072 2.07353 3.4906V5.13008C2.07353 5.51852 1.84807 6.06402 1.57285 6.33903L0.413418 7.49825C-0.137806 8.04906 -0.137806 8.95092 0.413418 9.5026L1.57285 10.6618C1.84824 10.937 2.07353 11.4823 2.07353 11.8708V13.5103C2.07353 14.2893 2.71145 14.9272 3.49029 14.9272H5.12977C5.51929 14.9272 6.06371 15.1527 6.33892 15.4277L7.49815 16.5873C8.04896 17.1377 8.95082 17.1377 9.50163 16.5873L10.6608 15.4277C10.9362 15.1525 11.4804 14.9272 11.87 14.9272H13.5101C14.2897 14.9272 14.9268 14.2893 14.9268 13.5103V11.8708C14.9268 11.4806 15.1525 10.9368 15.4275 10.6618L16.5869 9.5026C17.1373 8.95092 17.1373 8.04902 16.5869 7.49821ZM7.3753 11.6877L4.24958 8.5616L5.25133 7.56005L7.37555 9.68426L11.7482 5.31261L12.7497 6.31416L7.3753 11.6877Z" fill="#ABF7B1"/>
                                                </g>`;
                            });
                            $('.success-message').html("").css({ 'display': 'none'});
                            elementsToBlur.forEach(id => {
                                const element = document.getElementById(id);
                                if (element) {
                                    element.style.filter = "blur(3px)";
                                    element.style.pointerEvents = "none";
                                }
                            });
                            pop_up_box_upgrade.style.display = "block";                       
                        } else {
                            $('#xml-heading').text('Upgrade to Pro Features').css('color', '#1d2327');
                            $('.content').css('border-top', '3px solid #FDB930');
                            $('.content .content-inside1 .xml-right-des ul li').css('background-color', '#fffbf3');
                            $('.content .content-inside1 .xml-right-des ul li').css('border', 'border: 0.5px solid #ffeecb');
                            var svg = document.querySelectorAll('.content .content-inside1 .xml-right-des ul li svg');
                            svg.forEach(function(svgElement) {
                                svgElement.innerHTML = `<g clip-path="url(#clip0_1642_43)">
                                <path d="M16.5871 7.49821L15.4277 6.33898C15.1525 6.06381 14.927 5.52001 14.927 5.13004V3.4906C14.927 2.71072 14.2899 2.07363 13.5102 2.07321H11.8701C11.4806 2.07321 10.9362 1.8473 10.661 1.57233L9.50173 0.413107C8.95092 -0.137702 8.04907 -0.137702 7.49826 0.413107L6.33903 1.57316C6.06361 1.84834 5.51857 2.07363 5.12988 2.07363H3.49044C2.7116 2.07363 2.07368 2.71072 2.07368 3.4906V5.13008C2.07368 5.51852 1.84822 6.06402 1.573 6.33903L0.413571 7.49825C-0.137653 8.04906 -0.137653 8.95092 0.413571 9.5026L1.573 10.6618C1.84839 10.937 2.07368 11.4823 2.07368 11.8708V13.5103C2.07368 14.2893 2.7116 14.9272 3.49044 14.9272H5.12992C5.51944 14.9272 6.06386 15.1527 6.33907 15.4277L7.4983 16.5873C8.04911 17.1377 8.95097 17.1377 9.50178 16.5873L10.661 15.4277C10.9364 15.1525 11.4806 14.9272 11.8702 14.9272H13.5103C14.2899 14.9272 14.927 14.2893 14.927 13.5103V11.8708C14.927 11.4806 15.1527 10.9368 15.4277 10.6618L16.5871 9.5026C17.1375 8.95092 17.1375 8.04902 16.5871 7.49821ZM7.37545 11.6877L4.24973 8.5616L5.25148 7.56005L7.3757 9.68426L11.7484 5.31261L12.7499 6.31416L7.37545 11.6877Z" fill="#FDBC33"/>
                                </g>`;
                            });
                            $('.success-message').html("Code added incorrectly. Pro feature disabled. Please review and correct.").css({ 'display': 'block', 'color': 'red' });
                        }
                    }else{
                        $('.success-message').html("Please select valid page/post.").css({ 'display': 'block', 'color': 'red' });            
                    }
                },
                error: function (xhr, status, error) {
                    $('.loader').css('display', 'none');
                }
            });
        } else {
            $('.success-message').html("Please select page/post.").css({ 'display': 'block', 'color': 'red' });
        }
    });

    // For post type XML tab content
    const tabContents = document.querySelectorAll('.tab-content-post-type');
    tabContents.forEach(content => {
        content.style.display = 'none';
    });

    const tabs = document.querySelectorAll('.post-type-tab');
    tabs.forEach((tab, index) => {
        if (index === 0) {
            tab.classList.add('active');
            const tabContentId = tab.getAttribute('href').replace('#', '');
            document.getElementById(tabContentId).style.display = 'block';
        }
        tab.addEventListener('click', function (event) {
            event.preventDefault();
            event.stopPropagation();
            const tabContentId = this.getAttribute('href').replace('#', '');
            tabContents.forEach(content => {
                content.style.display = 'none';
            });
            document.getElementById(tabContentId).style.display = 'block';
            tabs.forEach(tab => {
                tab.classList.remove('active');
            });
            this.classList.add('active');
        });
    });

    // For archive page XML tab content
    const tabContentsArchive = document.querySelectorAll('.tab-content-archive-page');
    tabContentsArchive.forEach(content => {
        content.style.display = 'none';
    });

    const tabArchive = document.querySelectorAll('.archive-type-tab');
    const firstTab = tabArchive[0];
    const firstTabContentId = firstTab?.getAttribute('href').replace('#', '');

    tabArchive.forEach(tab => {
        tab.addEventListener('click', function (event) {
            event.preventDefault();
            event.stopPropagation();
            const tabContentId = this.getAttribute('href').replace('#', '');
            tabContentsArchive.forEach(content => {
                content.style.display = 'none';
            });
            document.getElementById(tabContentId).style.display = 'block';
            tabArchive.forEach(tab => {
                tab.classList.remove('active');
            });
            this.classList.add('active');
        });
    });
    firstTab?.classList.add('active');
    if (firstTabContentId) {
        document.getElementById(firstTabContentId).style.display = 'block';
    }

    // For post type tab HTML content
    var tabLinksOptions = jQuery('.html-sitemap-wrapper a');
    var tabContentsOptions = jQuery('.tab-content-html-sitemap');

    for (var i = 1; i < tabContentsOptions.length; i++) {
        jQuery(tabContentsOptions[i]).hide();
    }

    jQuery(tabLinksOptions[0]).addClass('active');
    jQuery(tabContentsOptions[0]).addClass('active').show();

    tabLinksOptions.each(function (index, tabLink) {
        jQuery(tabLink).on('click', function (e) {
            e.preventDefault();

            tabLinksOptions.removeClass('active');
            tabContentsOptions.hide().removeClass('active');

            var targetId = jQuery(tabLink).attr('href').replace('#', '');
            jQuery('#' + targetId).slideDown().addClass('active');
            jQuery(tabLink).addClass('active');
        });
    });

    // For post type tab HTML content
    var tabLinks = document.querySelectorAll('.post-type-html-wrapper a');
    var tabContentsPostTypeHTML = document.querySelectorAll('.tab-content-post-type-html');

    for (var i = 1; i < tabContentsPostTypeHTML.length; i++) {
        tabContentsPostTypeHTML[i].style.display = 'none';
    }

    tabLinks[0]?.classList.add('active');
    tabContentsPostTypeHTML[0]?.classList.add('active');
    if (tabContentsPostTypeHTML[0]) {
        tabContentsPostTypeHTML[0].style.display = 'block';
    }

    tabLinks.forEach(function (tabLink, index) {
        tabLink.addEventListener('click', function (e) {
            e.preventDefault();

            tabLinks.forEach(function (link) {
                link.classList.remove('active');
            });

            tabContentsPostTypeHTML.forEach(function (tabContent) {
                tabContent.style.display = 'none';
                tabContent.classList.remove('active');
            });

            var targetId = tabLink.getAttribute('href').replace('#', '') + '-html';
            var targetContent = document.getElementById(targetId);
            if (targetContent) {
                targetContent.style.display = 'block';
                tabLink.classList.add('active');
                targetContent.classList.add('active');
            }
        });
    });
    
    // For Upgrade to Premium Page
    var backlinkRadio = document.querySelector('input[name="upgrade_option"][value="backlink"]');
    var premiumRadio = document.querySelector('input[name="upgrade_option"][value="premium"]');
    var backlinkDiv = document.getElementById('backlink');
    var premiumDiv = document.getElementById('premium');
    const validateButtonDiv = document.getElementById('validate-button');

    function showHideDiv() {
        if (backlinkRadio?.checked) {
            backlinkDiv.style.display = 'block';
            premiumDiv.style.display = 'none';
        } else if (premiumRadio?.checked) {
            backlinkDiv.style.display = 'none';
            premiumDiv.style.display = 'block';
        } else {
            if (backlinkDiv) {
                backlinkDiv.style.display = 'block';
            }
            if (premiumDiv) {
                premiumDiv.style.display = 'none';
            }
        }
    }

    backlinkRadio?.addEventListener('change', showHideDiv);
    premiumRadio?.addEventListener('change', showHideDiv);
    showHideDiv();

    if (premiumRadio) {
        premiumRadio.addEventListener('change', function () {
            if (this.checked) {
                validateButtonDiv.style.display = 'none';
            }
        });
    }
    if (backlinkRadio) {
        backlinkRadio.addEventListener('change', function () {
            if (this.checked) {
                validateButtonDiv.style.display = 'block';
            }
        });
    }

    // To fetch saved post title in input box
    function fetchPostTitle() {
        var permalink = document.getElementById('selected_post_permalink')?.value;
        var data = {
            'action': 'get_post_title',
            'permalink': permalink
        };
        jQuery.post(ajaxurl, data, function(response) {
            document.getElementById('select_posts_input')?.setAttribute('value', response);		
        });
    }
    fetchPostTitle();

    // To blur / unblur HTML tab based on premium access

    if(premium_access == 0){
        var htmlSitemap = document.getElementById("html_sitemap");
        if (htmlSitemap) {
            htmlSitemap.style.filter = "blur(3px)";
            htmlSitemap.style.pointerEvents = "none";
        } 
    }else{
        var checkboxHTML = document.getElementById('xmlsbw_enable_html_sitemap');
        var enableAdvancedSettingscheckboxHTML = document.getElementById('xmlsbw_enable_advanced_settings_html');
        var rowsToToggleTest = ['display_html_sitemap_tr', 'sort_order_tr', 'sort_direction_tr', 'select_post_type_html_tr',
            'include_publication_date_tr', 'enable_advanced_settings_html_tr', 'exclude_posts_html_tr', 'exclude_terms_html_tr', 'compact_archives_tr'];

        function toggleRowsHTML() {
            var displayValueTest = checkboxHTML?.checked ? '' : 'none';
            var advancedSettingsEnabledTest = enableAdvancedSettingscheckboxHTML?.checked;
            rowsToToggleTest.forEach(function (rowId) {
                var rowTest = document.getElementById(rowId);
                if (rowTest) {
                    if ((rowId === 'exclude_posts_html_tr' || rowId === 'exclude_terms_html_tr') && !advancedSettingsEnabledTest) {
                        rowTest.style.display = 'none';
                    } else {
                        rowTest.style.display = displayValueTest;
                    }
                }
            });
        }

        toggleRowsHTML();
        checkboxHTML?.addEventListener('change', toggleRowsHTML);
        enableAdvancedSettingscheckboxHTML?.addEventListener('change', toggleRowsHTML);
    }

    // To show/hide XML sitemap settings based on enable/disable
    var checkbox = document.getElementById('xmlsbw_enable_sitemap_generation');
    var enableIndexesCheckbox = document.getElementById('xmlsbw_enable_sitemap_indexes');
    var enableAdvancedSettingsCheckbox = document.getElementById('xmlsbw_enable_advanced_settings');
    var enableAdditionalPagesCheckbox = document.getElementById('xmlsbw_enable_additional_pages');
    var rowsToToggle = ['rename_sitemap_url_tr', 'enable_sitemap_indexes_tr', 'xml_preview_tr', 'select_post_type_tr',
        'select_archive_page_tr', 'include_last_mod_time_tr', 'post_priority_calculation_tr', 'enable_advanced_settings_tr', 'enable_additional_pages_tr',
        'links_per_sitemap_tr', 'exclude_posts_tr', 'exclude_terms_tr', 'additional_pages_tr'];

    function toggleRows() {
        var displayValue = checkbox?.checked ? '' : 'none';
        var advancedSettingsEnabled = enableAdvancedSettingsCheckbox?.checked;
        var additionalPagesEnabled = enableAdditionalPagesCheckbox?.checked;
        rowsToToggle.forEach(function (rowId) {
            var row = document.getElementById(rowId);
            if (row) {
                if ((rowId === 'exclude_posts_tr' || rowId === 'exclude_terms_tr') && !advancedSettingsEnabled) {
                    row.style.display = 'none';
                } else if (rowId === 'additional_pages_tr' && !additionalPagesEnabled) {
                    row.style.display = 'none';
                } else {
                    row.style.display = displayValue;
                }
            }
        });
    }

    toggleRows();
    checkbox?.addEventListener('change', toggleRows);
    enableIndexesCheckbox?.addEventListener('change', toggleRows);
    enableAdvancedSettingsCheckbox?.addEventListener('change', toggleRows);
    enableAdditionalPagesCheckbox?.addEventListener('change', toggleRows);

});
