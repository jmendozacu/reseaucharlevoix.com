/**
 * Created by Nguyen on 4/8/2016.
 */
app.directive('productView', ['Product', function (Product) {
    return {
        restrict: 'E',
        templateUrl: SKIN_URL + '/frontend/base/default/benova/toolbox-designer/app/src/directives/product-view.phtml',
        scope: true,

        controller: function ($scope, $http, $httpParamSerializer) {
            $scope.clear = function () {
                $scope.qty = 1;
                $scope.orderLabel = 'FINISHED WITH CHOICE';
                $scope.successedAddToCart = false;
                $scope.selections = [];
                $scope.dataPost = {
                    bundle_option: {},
                    bundle_option_qty: {}
                }
            }
            $scope.clear();
            $scope.addSelection = function (product) {
                if ($scope.getSpendSize() == $scope.getMaxSize()) {
                    alert('Drawer was full.');
                    return;
                }
                if (!$scope.isSelectedMe(product)) {
                    product.sizeScale = $scope.getSize(product);
                    $scope.selections.push(product);
                    return;
                }

                var index = _.indexOf($scope.selections, product);
                $scope.selections.splice(index,1);
            };


            $scope.isSelectedMe = function (product) {
                return _.indexOf($scope.selections, product) != -1;
            }
            $scope.getMaxSize = function () {
                return $scope.curentProduct.number_of_drawers * $scope.curentProduct.size_per_drawer;
            }
            $scope.getSpendSize = function () {
                return _.sumBy($scope.selections, function (selection) {
                    return selection && selection.sizeScale ? selection.sizeScale * 1 : 0;
                });
            }
            $scope.getSpendSizeInfo = function () {
                return _.toInteger($scope.getSpendSize() % $scope.curentProduct.size_per_drawer);
            }

            $scope.getAvailableSize = function () {
                return $scope.getMaxSize() - $scope.getSpendSize();
            }

            $scope.getSpendDrawer = function () {
                return _.toInteger($scope.getSpendSize() / $scope.curentProduct.size_per_drawer);
            }

            $scope.getAvailableDrawer = function () {
                return $scope.curentProduct.number_of_drawers - $scope.getSpendDrawer();
            }
            $scope.getSum = function () {
                $scope.dataPost.product = $scope.curentProduct.entity_id;
                $scope.dataPost.qty = $scope.qty;
                return _.sumBy($scope.selections, function (selection) {
                    if(!_.isObject($scope.dataPost.bundle_option[selection.option_id])){
                        $scope.dataPost.bundle_option[selection.option_id] = {};
                    }
                    $scope.dataPost.bundle_option[selection.option_id][selection.selection_id] = true;
                    $scope.dataPost.bundle_option_qty[selection.option_id] = 1;
                    return _.toNumber(selection.final_price);
                });
            }
            $scope.getSize = function (product) {
                var id = product.entity_id;
                var optionId = product.option_id;
                var string = $scope.curentProduct.drawer_info;
                var drawerData = JSON.parse(string ? string : '[]');
                var dataReturn = 0;
                if (drawerData && drawerData[optionId]) {
                    dataReturn = drawerData[optionId][id];
                }
                return dataReturn;
            }
            $scope.order = function () {
                /*
                 * array(7) {
                 ["uenc"] => string(88) "aHR0cDovL3Rob21hcy5sb2NhbC9ob21lLWRlY29yL2JlZC1iYXRoL3BpbGxvdy1hbmQtdGhyb3ctc2V0Lmh0bWw,"
                 ["product"] => string(3) "447"
                 ["form_key"] => string(16) "8VkD6gnj3YkbCRag"
                 ["related_product"] => string(0) ""
                 ["bundle_option"] => array(2) {
                 [24] => string(2) "91"
                 [23] => string(2) "89"
                 }
                 ["bundle_option_qty"] => array(2) {
                 [24] => string(1) "1"
                 [23] => string(1) "1"
                 }
                 ["qty"] => string(1) "1"
                 }*/
                $scope.orderLabel = 'WAITING...';
                $http.post(URL + 'toolbox-designer/index/add', $httpParamSerializer($scope.dataPost),
                    {
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                    }).then(function (res) {
                        if (res.data.success) {
                            $scope.clear();
                            $scope.successedAddToCart = true;
                            angular.element('.header-minicart').html(res.data.cart)
                            angular.element('#header-cart').html(res.data.cartItems)
                        }
                        if (!res.data.success) {
                            alert(res.data.message);
                        }
                    }, function (err) {
                        console.log(err)
                    })
            };
        }
    }
}])