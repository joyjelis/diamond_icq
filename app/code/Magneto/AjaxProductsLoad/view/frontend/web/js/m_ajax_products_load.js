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

                if (this.jsconfig.products_mobile) {
                    this.products(this.jsconfig.products_mobile);
                }

                if (this.jsconfig.filtercategories) {
                    this.setFilterCategory(this.jsconfig.filtercategories);
                }

                if (this.jsconfig.sidebarcategories) {
                    this.setSidebarCategory(this.jsconfig.sidebarcategories);
                }

                if (this.jsconfig.mobile_limit) {
                    this.limit(this.jsconfig.mobile_limit);
                }

                if (this.jsconfig.products_mobile_size) {
                    this.size(this.jsconfig.products_mobile_size);
                }

                this.displayviewmore();

                var self = this;
                this.activesidebarcategory.subscribe(function(newValue) {
                    self.selectsidebarcategory(newValue);
                });

                this.activefiltercategory.subscribe(function(newValue) {
                    self.selectfiltercategory(newValue);
                });

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

            selectfiltercategory: function(id) {
                this.setActiveFilterCategory(id);
                this.updateproducts();
            },

            selectsidebarcategory: function(id) {
                this.setActiveSidebarCategory(id);
                this.setActiveFilterCategory("");
                var self = this;
                _.each(this.sidebarcategories(), function(data, index) {
                    if (data.id == id) {
                        self.setactivelink(data.url);
                    }
                });

                this.updateproducts();
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