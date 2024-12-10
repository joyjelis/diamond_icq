define(['jquery', 'underscore', 'uiComponent', 'ko'],
    function($, _, Component, ko) {
        'use strict'

        return Component.extend({
            isLoading: ko.observable(true),
            category_name: ko.observable(""),
            producthtml: ko.observable(""),
            productdata: ko.observableArray([]),
            sidebarcategories: ko.observableArray([]),
            filtercategories: ko.observableArray([]),
            products: ko.observable(""),
            callbackurl: ko.observable(""),
            activecategorylink: ko.observable(""),
            activesidebarcategory: ko.observable(""),
            activefiltercategory: ko.observable(""),
            isproductDataExits: ko.observable(""),
            limit: ko.observable(""),
            size: ko.observable(""),
            viewmore: ko.observable(false),

            initialize: function(config) {
                this._super();
                this.jsconfig = JSON.parse(atob(config.jsconfig));

                if (this.jsconfig.category_name) {
                    this.category_name(this.jsconfig.category_name);
                }

                if (this.jsconfig.products) {
                    this.products(this.jsconfig.products);
                }

                if (this.jsconfig.filtercategories) {
                    this.setFilterCategory(this.jsconfig.filtercategories);
                }

                if (this.jsconfig.sidebarcategories) {
                    this.setSidebarCategory(this.jsconfig.sidebarcategories);
                }

                if (this.jsconfig.desktop_limit) {
                    this.limit(this.jsconfig.desktop_limit);
                }

                if (this.jsconfig.products_size) {
                    this.size(this.jsconfig.products_size);
                }

                this.displayviewmore();

                this.callbackurl(this.jsconfig.callbackurl);
                this.initsetproductdata();
                this.setactivehtml()
            },

            displayviewmore: function() {
                this.viewmore(false);
                if (this.limit() == this.size()) {
                    this.viewmore(true);
                }
            },

            setactivelink: function(url) {
                this.activecategorylink(url);
            },

            setactivehtml: function() {
                var index = this.getActiveIndex();
                this.setproducthtml(index);
            },

            getActiveIndex: function() {
                return this.activefiltercategory() + this.activesidebarcategory();
            },

            initsetproductdata: function() {
                var html = {
                    index: this.getActiveIndex(),
                    taghtml: this.products(),
                    size: this.size()
                };

                this.productdata.push(html);
            },

            setproducthtml: function(id) {
                var self = this;
                _.each(self.productdata(), function(data, index) {
                    if (data.index == id) {
                        self.isproductDataExits(1)
                        self.producthtml(data.taghtml);
                        self.size(data.size);
                    }
                });
            },

            selectfiltercategory: function(data, event) {
                var context = ko.contextFor(event.target);

                if (data.id == context.$parent.activefiltercategory()) {
                    context.$parent.setActiveFilterCategory("");
                } else {
                    context.$parent.setActiveFilterCategory(data.id);
                }

                context.$parent.updateproducts();
                if (event.ctrlKey) {
                    window.open(data.url, '_blank').focus();
                }
            },

            selectsidebarcategory: function(data, event) {
                var context = ko.contextFor(event.target);
                if (data.id != context.$parent.activesidebarcategory()) {
                    context.$parent.setActiveSidebarCategory(data.id);
                    context.$parent.setActiveFilterCategory("");
                }

                context.$parent.updateproducts();
                context.$parent.setactivelink(data.url);
                if (event.ctrlKey) {
                    window.open(data.url, '_blank').focus();
                }
            },

            updateproducts: function() {
                this.isproductDataExits(0);
                var index = this.getActiveIndex();
                this.setproducthtml(index);
                if (!this.isproductDataExits()) {
                    this.fetchproductdata();
                }

                this.displayviewmore();
            },

            fetchproductdata: function() {
                var category_filter = {
                    'currentactive': this.activesidebarcategory(),
                    'currenthorizontalactive': this.activefiltercategory(),
                    'limit': this.limit()
                };

                var self = this;
                $.ajax({
                    showLoader: true,
                    url: self.callbackurl(),
                    data: category_filter,
                    cache: false,
                    type: "POST",
                    dataType: 'json'
                }).done(function(data) {
                    if (data.success) {
                        var htmldata = {
                            index: self.getActiveIndex(),
                            taghtml: data.html,
                            size: data.size
                        };

                        self.size(data.size);
                        self.productdata.push(htmldata);
                        self.displayviewmore();
                        self.setactivehtml();
                    }
                });
            },

            setFilterCategory: function(filtercategories) {
                if (filtercategories.hasOwnProperty('active')) {
                    this.setActiveFilterCategory(filtercategories.active);
                }

                if (filtercategories.hasOwnProperty('category')) {
                    var self = this;
                    _.each(filtercategories.category, function(data, index) {
                        var cat = {
                            id: data.id,
                            name: data.name,
                            url: data.url_key
                        };

                        self.filtercategories.push(cat);
                    });
                }
            },

            setActiveFilterCategory: function(activefiltercategory) {
                this.activefiltercategory(activefiltercategory);
            },

            setSidebarCategory: function(sidebarcategories) {
                if (sidebarcategories.hasOwnProperty('active')) {
                    this.setActiveSidebarCategory(sidebarcategories.active);
                }

                if (sidebarcategories.hasOwnProperty('category')) {
                    var self = this;
                    var activated = sidebarcategories.active;
                    _.each(sidebarcategories.category, function(data, index) {
                        var cat = {
                            id: data.id,
                            name: data.name,
                            url: data.url_key
                        };

                        if (data.id == activated) {
                            self.setactivelink(data.url_key);
                        }

                        self.sidebarcategories.push(cat);
                    });
                }
            },

            setActiveSidebarCategory: function(activesidebarcategory) {
                this.activesidebarcategory(activesidebarcategory);
            },

        });
    });