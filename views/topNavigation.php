 <div class="top_nav col-md-12" style="min-height: 62px;">
     <div class="nav_menu" style="min-height: 62px; padding: 0 15px;">
         <nav style="display: flex; align-items: center; justify-content: space-between; min-height: 62px;">
             <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-top: 0;">

                 <div class="btn-group btn-group-sm" role="group" aria-label="Filtro de status" style="display: flex; align-items: center;">
                     <label class="btn btn-default active project-filter-label" data-status="EM-ANDAMENTO" style="padding: 2px 8px; line-height: 1.2; border-color: #f0ad4e; color: #8a6d3b;">
                         <input type="checkbox" class="project-status-filter" value="EM-ANDAMENTO" checked style="margin-right: 4px;"> EM-ANDAMENTO
                     </label>
                     <label class="btn btn-default active project-filter-label" data-status="FINALIZADO" style="padding: 2px 8px; line-height: 1.2; border-color: #5cb85c; color: #3c763d;">
                         <input type="checkbox" class="project-status-filter" value="FINALIZADO" checked style="margin-right: 4px;"> FINALIZADO
                     </label>
                     <label class="btn btn-default active project-filter-label" data-status="EM-ANALISE" style="padding: 2px 8px; line-height: 1.2; border-color: #5bc0de; color: #31708f;">
                         <input type="checkbox" class="project-status-filter" value="EM-ANALISE" checked style="margin-right: 4px;"> EM-ANALISE
                     </label>
                     <label class="btn btn-default active project-filter-label" data-status="PENDENTE" style="padding: 2px 8px; line-height: 1.2; border-color: #d9534f; color: #a94442;">
                         <input type="checkbox" class="project-status-filter" value="PENDENTE" checked style="margin-right: 4px;"> PENDENTE
                     </label>
                 </div>
             </div>

             <ul class="nav navbar-nav navbar-right" style="margin-bottom: 0;">
                 <li class="">
                     <a href="javascript:;" class="user-profile dropdown-toggle"
                         data-toggle="dropdown"
                         aria-expanded="false" style="padding: 0 10px; line-height: 62px;">
                         John Doe
                     </a>
                     <ul class="dropdown-menu dropdown-usermenu pull-right">
                         <li><a href="login.html"><i class="fa fa-sign-out pull-right"></i> Log
                                 Out</a></li>
                     </ul>
                 </li>
             </ul>
         </nav>
     </div>
 </div>

 <style>
 .project-filter-label {
     transition: background-color 0.2s ease, color 0.2s ease, border-color 0.2s ease;
 }
 .project-filter-label:hover {
     background-color: rgba(0, 0, 0, 0.05);
 }
 .project-filter-label[data-status="EM-ANDAMENTO"]:hover {
     background-color: rgba(240, 173, 78, 0.2);
 }
 .project-filter-label[data-status="FINALIZADO"]:hover {
     background-color: rgba(92, 184, 92, 0.2);
 }
 .project-filter-label[data-status="EM-ANALISE"]:hover {
     background-color: rgba(91, 192, 222, 0.2);
 }
 .project-filter-label[data-status="PENDENTE"]:hover {
     background-color: rgba(217, 83, 79, 0.2);
 }
 </style>

 <script>
 (function() {
     var storageKey = 'inovatec-project-status-filters';

     function normalizeStatus(value) {
         return String(value || '')
             .toUpperCase()
             .normalize('NFD')
             .replace(/[\u0300-\u036f]/g, '')
             .replace(/\s+/g, '-')
             .replace(/-+/g, '-');
     }

     function getStoredStatuses() {
         try {
             var stored = localStorage.getItem(storageKey);
             if (!stored) {
                 return null;
             }

             var parsed = JSON.parse(stored);
             return Array.isArray(parsed) ? parsed : null;
         } catch (error) {
             return null;
         }
     }

     function saveStoredStatuses(selectedStatuses) {
         try {
             localStorage.setItem(storageKey, JSON.stringify(selectedStatuses));
         } catch (error) {
             console.warn('Não foi possível salvar os filtros:', error);
         }
     }

     function initProjectFilters() {
         var filters = Array.prototype.slice.call(document.querySelectorAll('.project-status-filter'));
         if (!filters.length) {
             return;
         }

         var rows = Array.prototype.slice.call(document.querySelectorAll('.project-row'));
         var storedStatuses = getStoredStatuses();

         if (storedStatuses && storedStatuses.length) {
             filters.forEach(function(checkbox) {
                 var normalizedValue = normalizeStatus(checkbox.value);
                 checkbox.checked = storedStatuses.indexOf(normalizedValue) !== -1;
             });
         } else {
             filters.forEach(function(checkbox) {
                 checkbox.checked = true;
             });
         }

         function applyProjectFilters() {
             var selectedStatuses = filters
                 .filter(function(checkbox) {
                     return checkbox.checked;
                 })
                 .map(function(checkbox) {
                     return normalizeStatus(checkbox.value);
                 });

             rows.forEach(function(row) {
                 var status = normalizeStatus(row.getAttribute('data-status') || 'PENDENTE');
                 var shouldShow = selectedStatuses.length === 0 || selectedStatuses.indexOf(status) !== -1;
                 row.style.display = shouldShow ? '' : 'none';
             });

             saveStoredStatuses(selectedStatuses);
         }

         filters.forEach(function(checkbox) {
             checkbox.addEventListener('change', applyProjectFilters);
         });

         applyProjectFilters();
     }

     if (document.readyState === 'loading') {
         document.addEventListener('DOMContentLoaded', initProjectFilters);
     } else {
         initProjectFilters();
     }
 })();
 </script>
