// Controller for the Add User dialog.
var AddFolioDialogController = function ($scope, $modalInstance, entitlementService, guid) {
  // Placeholder for ie. Need to set at timeout otherwise the password fields can't be retrieved
  $scope.partialInitHandler = function() {
    setTimeout(function() {
      $("input").placeholder();
    }, 10);
  };

  // Data storage for the user name and description.
  $scope.form = {};

  $scope.ok_clickHandler = function () {
    if (!$scope.form.pubName) { // Make sure the fields are not empty.
      $scope.form.errorMessage = "Please enter a product label.";
    } else if (!$scope.form.folioNumber) {
      $scope.form.errorMessage = "Please enter a product description.";
    } else if (!$scope.form.productId) {
      $scope.form.errorMessage = "Please enter a product ID.";
    } else if (!$scope.form.pubDate) {
      $scope.form.errorMessage = "Please enter a valid availability date: YYYY-MM-DD.";
    } else {
      $scope.form.isAddingFolio = true;

      entitlementService.addFolio(guid, $scope.form.pubName, $scope.form.folioNumber, $scope.form.productId, $scope.form.pubDate).then(
        function(data) {
          $scope.form.isAddingFolio = false;
          if (data.success) {
            // Assign the user id returned from the request.
            $scope.form.id = data.id;
            $modalInstance.close($scope.form);
          } else {
            $scope.form.errorMessage = data.description || "Sorry, unable to add this folio.";
          }
        },
        function() {
          $scope.form.isAddingFolio = false;
          $scope.form.errorMessage = "Sorry, unable to reach the database.";
        }
      );
    }
  };

  $scope.cancel_clickHandler = function () {
    $modalInstance.dismiss("cancel");
  };
};
