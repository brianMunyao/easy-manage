   </main>
   <footer>
       EasyManage &copy; 2023 All Rights Reserved.
   </footer>
   </div>

   <script>
       var date = new Date();
       new Date().setDate(date.getDate() + 2);
       var tomorrow = date.toISOString().split('T')[0];

       var dateInputs = document.querySelectorAll('input[type="date"]');
       dateInputs.forEach(function(input) {
           input.min = tomorrow;
       });
   </script>

   </body>

   </html>