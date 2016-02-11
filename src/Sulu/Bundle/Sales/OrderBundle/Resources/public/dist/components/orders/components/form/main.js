define(["sulusalesorder/util/sidebar","sulusalesorder/util/orderStatus","sulusalesorder/util/header","sulusalescore/util/helper","sulucontact/models/account","config","widget-groups"],function(a,b,c,d,e,f,g){"use strict";var h="#order-form",i={currencyCode:"EUR"},j={accountContactsUrl:"/admin/api/accounts/<%= id %>/contacts?flat=true",accountAddressesUrl:"/admin/api/accounts/<%= id %>/addresses",accountInputId:"#account-input",deliveryAddressInstanceName:"delivery-address",billingAddressInstanceName:"billing-address",currencySelectInstanceName:"currency-select",currencySelectSelector:"#currency-select",itemTableInstanceName:"order-items",itemTableSelector:"#order-items",paymentTermsInstanceName:"payment-terms",deliveryTermsInstanceName:"delivery-terms",contactSelectId:"#contact-select",validateWarningTranslation:"form.validation-warning",translationShippingFailed:"salescore.shipping-failed",autocompleteLimit:20},k=function(){return this.options.data&&this.options.data.status?this.options.data.status.id:null},l=function(a){this.options.orderStatuses=a},m=function(a){this.options.currencies=a},n=function(){this.sandbox.on("sulu.salesorder.set-order-status",l.bind(this)),this.sandbox.on("sulu.salesorder.set-currencies",m.bind(this)),this.sandbox.on("husky.auto-complete."+this.accountInstanceName+".initialized",function(){this.isEditable||this.sandbox.dom.attr(this.$find(j.accountInputId+" input"),"disabled","disabled"),this.dfdAutoCompleteInitialized.resolve()},this),this.sandbox.on("husky.auto-complete."+this.accountInstanceName+".selection-removed",z.bind(this)),this.sandbox.on("sulu.salesorder.order.saved",function(a){this.options.data=a,q.call(this,!0),this.dfdFormSaved.resolve()},this),this.sandbox.on("sulu.toolbar.save",function(){this.submit()},this),this.sandbox.on("sulu.header.back",function(){this.sandbox.emit("sulu.salesorder.orders.list")},this),this.sandbox.on("husky.input.shipping-date.initialized",function(){this.dfdShippingDate.resolve()},this),this.sandbox.on("husky.input.order-date.initialized",function(){this.dfdOrderDate.resolve()},this),this.sandbox.on("husky.auto-complete."+this.accountInstanceName+".select",z.bind(this)),this.sandbox.on("sulu.editable-data-row."+j.deliveryAddressInstanceName+".initialized",function(){this.dfdDeliveryAddressInitialized.resolve()}.bind(this)),this.sandbox.on("sulu.editable-data-row."+j.billingAddressInstanceName+".initialized",function(){this.dfdInvoiceAddressInitialized.resolve()}.bind(this)),this.sandbox.on("sulu.editable-data-row.address-view."+j.deliveryAddressInstanceName+".changed",function(a){this.options.data.deliveryAddress=a,s.call(this,this.options.data),A.call(this)}.bind(this)),this.sandbox.on("sulu.editable-data-row.address-view."+j.billingAddressInstanceName+".changed",function(a){this.options.data.invoiceAddress=a,s.call(this,this.options.data),A.call(this)}.bind(this)),this.sandbox.on("husky.select."+j.currencySelectInstanceName+".selected.item",function(a){this.sandbox.emit("sulu.item-table."+j.itemTableInstanceName+".change-currency",a),this.currency=a},this),this.sandbox.on("sulu.salesorder.set-customer-id",function(a){this.customerId=a},this)},o=function(){this.sandbox.dom.on(this.$el,"click",p.bind(this),"#tax-free")},p=function(a){var b=$(a.currentTarget).is(":checked");this.sandbox.emit("sulu.item-table."+j.itemTableInstanceName+".update-price",b)},q=function(a){a!==this.saved&&(a?c.disableSave.call(this):c.enableSave.call(this)),this.saved=a},r=function(a){var b=this.sandbox.form.create(h);b.initialized.then(function(){s.call(this,a,!0),t.call(this,a)}.bind(this))},s=function(a){this.sandbox.form.setData(h,a).then(function(){this.accountId=u.call(this)}.bind(this)).fail(function(a){this.sandbox.logger.error("An error occured when setting data!",a)}.bind(this))},t=function(a){this.sandbox.start(h),a.customerAccount&&a.customerAccount.id&&B.call(this,a.customerAccount.id,a);var b=f.get("sulucontact.components.autocomplete.default.account");b.el=j.accountInputId,b.value=a.customerAccount?a.customerAccount:"",b.instanceName=this.accountInstanceName,b.remoteUrl+="&type="+this.customerId+"&limit="+j.autocompleteLimit,b.limit=j.autocompleteLimit,this.sandbox.start([{name:"auto-complete@husky",options:b}])},u=function(){return this.sandbox.dom.attr(j.accountInputId,"data-id")},v=function(a,b){b=b||[],this.sandbox.emit("husky.select.contact-select.update",a,b)},w=function(a,b,c){this.sandbox.emit("sulu.editable-data-row."+b+".data.update",a,c)},x=function(a,b){this.sandbox.emit("sulu.editable-data-row."+a+".set-value",b)},y=function(a,b){this.sandbox.emit("sulu.item-table."+j.itemTableInstanceName+".set-addresses",a,b)},z=function(a){var b=a.id||null;b!==this.accountId&&(this.accountId=b,b?B.call(this,b):(v.call(this,[]),w.call(this,[],j.deliveryAddressInstanceName),w.call(this,[],j.billingAddressInstanceName),y.call(this,[])))},A=function(){q.call(this,!1)},B=function(a,b){var c,d,f,g;f=e.findOrCreate({id:a}),f.fetch({success:function(a){f=a.toJSON();var c=null,e=null;b||(f.hasOwnProperty("termsOfDelivery")&&f.termsOfDelivery&&(e=f.termsOfDelivery.terms),x.call(this,j.deliveryTermsInstanceName,e),f.hasOwnProperty("termsOfPayment")&&f.termsOfPayment&&(c=f.termsOfPayment.terms),x.call(this,j.paymentTermsInstanceName,c)),f.hasOwnProperty("addresses")&&(g=f.addresses,d=null,b&&b.deliveryAddress?d=b.deliveryAddress:(d=D.call(this,g,"deliveryAddress",!0),!d&&g.length>0&&(d=g[0])),this.sandbox.data.when(this.dfdDeliveryAddressInitialized).then(function(){w.call(this,g,j.deliveryAddressInstanceName,d),y.call(this,g,d),this.options.data.deliveryAddress=d}.bind(this)),d=null,b&&b.invoiceAddress?d=b.invoiceAddress:(d=D.call(this,g,"billingAddress",!0),!d&&g.length>0&&(d=g[0])),this.sandbox.data.when(this.dfdInvoiceAddressInitialized).then(function(){w.call(this,g,j.billingAddressInstanceName,d),this.options.data.invoiceAddress=d}.bind(this)))}.bind(this),error:function(){this.sandbox.emit("sulu.labels.warning.show",this.sandbox.translate("error while fetching account"))}.bind(this)}),this.sandbox.util.load(this.sandbox.util.template(j.accountContactsUrl,{id:a})).then(function(a){c=a._embedded.contacts,d=b&&b.customerContact?[b.customerContact.id]:null,v.call(this,c,d)}.bind(this)).fail(function(a,b){this.sandbox.logger.error(a,b)}.bind(this))},C=function(a,b){var c=[];return this.sandbox.util.each(b,function(d){return b[d].code===a?(c.push(b[d].id),!1):void 0}.bind(this)),c},D=function(a,b,c){var d=null;return a&&a.length>0&&this.sandbox.util.each(a,function(a,e){return e.hasOwnProperty(b)&&e[b]===c?(d=e,!1):void 0}.bind(this)),d};return{view:!0,layout:{content:{width:"fixed"},sidebar:{width:"max",cssClasses:"sidebar-padding-50"}},templates:["/admin/order/template/order/form"],initialize:function(){this.saved=!0,this.formId=h,this.accountId=null,this.contactId=null,this.dfdFormSaved=this.sandbox.data.deferred(),this.dfdAllFieldsInitialized=this.sandbox.data.deferred(),this.dfdAutoCompleteInitialized=this.sandbox.data.deferred(),this.dfdShippingDate=this.sandbox.data.deferred(),this.dfdOrderDate=this.sandbox.data.deferred(),this.dfdInvoiceAddressInitialized=this.sandbox.data.deferred(),this.dfdDeliveryAddressInitialized=this.sandbox.data.deferred(),this.sandbox.data.when(this.dfdShippingDate,this.dfdOrderDate,this.dfdAutoCompleteInitialized).then(function(){this.dfdAllFieldsInitialized.resolve()}.bind(this)),this.orderStatusId=k.call(this),this.isEditable=this.orderStatusId<=b.IN_CART,this.options.data=this.sandbox.util.extend({},i,this.options.data);var d=this.options.data.id?this.options.data.id:"new";this.accountInstanceName="customerAccount"+d,n.call(this),o.call(this),c.initialize.call(this),q.call(this,!0),this.render(),c.setToolbar.call(this,this.options.data),this.listenForChange(),this.options.data&&this.options.data.id&&g.exists("order-detail")&&a.initForDetail(this.sandbox,this.options.data)},render:function(){this.sandbox.dom.html(this.$el,this.renderTemplate(this.templates[0],{isEditable:this.isEditable,parseDate:d.parseDate}));var a=this.options.data;r.call(this,a),this.startItemTableAndCurrencySelect()},startItemTableAndCurrencySelect:function(){this.sandbox.start([{name:"item-table@sulusalescore",options:{instanceName:j.itemTableInstanceName,isEditable:this.isEditable,remoteUrl:j.accountUrl,data:this.options.data.items,currency:this.options.data.currencyCode,el:j.itemTableSelector,settings:{columns:["addresses","description","quantity","single-price","delivery-date","cost-center","discount","tax-rate"]},taxfree:this.options.data.taxfree,deliveryCost:this.options.data.deliveryCost,enableDeliveryCost:!1}},{name:"select@husky",options:{el:j.currencySelectSelector,instanceName:j.currencySelectInstanceName,disabled:!this.isEditable,emitValues:!0,defaultLabel:this.sandbox.translate("dropdown.please-choose"),multipleSelect:!1,repeatSelect:!1,valueName:"code",data:this.options.currencies,preSelectedElements:C.call(this,this.options.data.currencyCode,this.options.currencies)}}])},submit:function(){if(this.dfdFormSaved=this.sandbox.data.deferred(),this.sandbox.logger.log("save Model"),this.sandbox.form.validate(h)){var a=this.sandbox.form.getData(h);""===a.id&&delete a.id,a.currencyCode=this.currency?this.currency:this.options.data.currencyCode,a.customerAccount={id:this.sandbox.dom.attr("#"+this.accountInstanceName,"data-id")},this.sandbox.logger.log("log data",a),this.sandbox.emit("sulu.salesorder.order.save",a)}else this.sandbox.emit("sulu.labels.warning.show",this.sandbox.translate(j.validateWarningTranslation)),this.dfdFormSaved.reject();return this.dfdFormSaved},listenForChange:function(){this.sandbox.data.when(this.dfdAllFieldsInitialized).then(function(){this.sandbox.dom.on(h,"change",A.bind(this),".changeListener select, .changeListener input, .changeListener .pickdate, .changeListener .husky-select, .changeListener textarea"),this.sandbox.dom.on(h,"keyup",A.bind(this),".changeListener select, .changeListener input, .changeListener textarea"),this.sandbox.on("sulu.item-table.changed",A.bind(this))}.bind(this))}}});