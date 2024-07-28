jQuery(document).ready(function($) {
+    // Import test
+    $('#import-test').on('change', function() {
+        var file = this.files[0];
+        var formData = new FormData();
+        formData.append('action', 'disc_assessment_import_test');
+        formData.append('import-test', file);
+        $.ajax({
+            url: ajaxurl,
+            type: 'POST',
+             formData,
+            processData: false,
+            contentType: false,
+            success: function(response) {
+                console.log(response);
+                // Handle success response
+            },
+            error: function(error) {
+                console.log(error);
+                // Handle error response
+            }
+        });
+    });
+
     // Export test
     $('.export-test').on('click', function(e) {
         e.preventDefault();
@@ -190,7 +216,7 @@
         var formData = new FormData();
         formData.append('action', 'disc_assessment_export_test');
         formData.append('test_id', testId);
-        $.ajax({
+        $.post({
             url: ajaxurl,
             type: 'POST',
              formData,
@@ -204,7 +230,7 @@
     });
 
     // Delete test
-    $('.delete-test').on('click', function(e) {
+    $('.delete-test').click(function(e) {
         e.preventDefault();
         var testId = $(this).data('test-id');
         if (confirm('Are you sure you want to delete this test?')) {
@@ -214,7 +240,7 @@
              {
                 action: 'disc_assessment_delete_test',
                 test_id: testId
-            },
+            }
         });
     }
     });
@@ -222,7 +248,7 @@
     // Edit test
     $('.edit-test').click(function(e) {
         e.preventDefault();
-        var testId = $(this).data('test-id');
+        var testId = $(this).attr('data-test-id');
         $.post({
             url: ajaxurl,
             type: 'POST',
@@ -232,7 +258,7 @@
              {
                 action: 'disc_assessment_edit_test',
                 test_id: testId
-            },
+            }
         });
     });
 });

