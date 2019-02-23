(function() {
    'use strict';

    angular
        .module('app.vehicle')
        .controller('VehicleController', VehicleController);

    VehicleController.$inject = [
        '$scope',
        '$state',
        'VehicleService',
        '$stateParams',
        '$rootScope',
        'editableOptions', 
        'editableThemes',
        '$filter',
        'TypeService',
        'SweetAlert'
    ];
    function VehicleController($scope,$state,VehicleService,$stateParams,$rootScope,editableOptions,editableThemes,$filter,TypeService,SweetAlert) {
        var vm = this;
        vm.id = $stateParams.id;

        activate();
        function activate() {
            vm.FormVehicle = {};
            vm.submitted = false;
            $scope.vehicleStatus = $rootScope.vehicleStatus;
            editableOptions.theme = 'bs3';

            
            editableThemes.bs3.inputClass = 'input-sm wd-sm';
            editableThemes.bs3.buttonsClass = 'btn-sm';
            editableThemes.bs3.submitTpl = '<button type="submit" class="btn btn-success"><span class="fa fa-check"></span></button>';
            editableThemes.bs3.cancelTpl = '<button type="button" class="btn btn-default" ng-click="$form.$cancel()">'+
                                               '<span class="fa fa-times text-muted"></span>'+
                                             '</button>';
            $scope.hstep = 1;
            $scope.mstep = 1;
            $scope.ismeridian = true; 
            $scope.seconds = 'hide'; 


            $scope.index = function () {
                VehicleService.fetch()
                .then(function(response) {
                    vm.vehicles = response.data;
                });
            };

            $scope.create = function(){
                TypeService.fetch()
                .then(function (response) {
                    $scope.types = response.data;
                });
            };
            
            vm.load = function(){
                // routes
                var id = $stateParams.id;
                VehicleService.fetch(id)
                .then(function (response) {
                    vm.title = response.data.title;
                    // vehicle details
                    vm.FormVehicle = response.data;
                    // schedules details
                    vm.schedules = response.data.schedules;
                    // seats
                    vm.seats = response.data.seats;
                });
            };

            $scope.edit = function(){
                $scope.disableform = false;
                var user = JSON.parse(localStorage.getItem('user'));
                if (user.user_type !== $rootScope.user_type.admin) {
                  $scope.disableform = true;
                } else {
                  $scope.disableform = false;
                }

                var id = $stateParams.id;
                VehicleService.fetch(id)
                .then(function (response) {
                    vm.title = response.data.title;
                    // vehicle details
                    vm.FormVehicle.title = response.data.title;
                    vm.FormVehicle.type_id = response.data.type_id;
                    vm.FormVehicle.capacity = response.data.capacity;
                    vm.FormVehicle.status = response.data.status;
                    vm.FormVehicle.description = response.data.description;
                    vm.FormVehicle.max_infant_count = response.data.max_infant_count;
                    // // schedules
                    // vm.schedules = response.data.schedules;
                    // seats
                    vm.seats = response.data.seats;
                });

                TypeService.fetch()
                .then(function (response) {
                    $scope.types = response.data;
                });
            };

            vm.validateInput = function(name, type) {
                var input = vm.formValidate[name];
                return (input.$dirty || vm.submitted) && input.$error[type];
            };

            vm.convertTime = function(time){
                return moment(time, "hh:mm:ss").format("hh:mm A");
            };

            vm.store = function(){
                vm.submitted = true;
                if (vm.formValidate.$valid) {
                    VehicleService.create(vm.FormVehicle)
                    .then(function (response){
                        if(response.success){
                            SweetAlert.swal('Success!',response.message,'success');
                            vm.FormVehicle = {};
                        }else{
                            SweetAlert.swal('Error!',response.message,'error');
                        }
                    });
                } else {
                    return false;
                }
            };

            vm.update = function () {
                vm.submitted = true;
                if (vm.formValidate.$valid) {
                    VehicleService.update(vm.id, vm.FormVehicle)
                    .then(function(response){
                        if (response.success) {
                            vm.title = vm.FormVehicle.title;
                            SweetAlert.swal('Success!', response.message, 'success');
                        } else {
                            SweetAlert.swal('Error!', response.message, 'error');
                        }
                    });
                }else {
                    return false;
                }
            };

            $scope.init= function(){
                if ($state.current.method !== undefined) {
                  $scope[$state.current.method]();
                } else {
                    $scope['index']();
                }
            };
            $scope.init();


        }
    }
})();