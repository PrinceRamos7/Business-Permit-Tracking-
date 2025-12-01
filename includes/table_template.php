<?php
/**
 * Modern Table Template
 * Generates consistent table structure across all pages
 */

function renderModernTable($title, $columns, $data, $actions_callback = null) {
    ?>
    <div class="card">
        <div class="card-body">
            <h4 class="card-title"><?php echo $title; ?></h4>
            
            <!-- Table Controls -->
            <div class="table-header">
                <div class="entries-control">
                    <span>Show</span>
                    <select class="entries-select">
                        <option value="5">5</option>
                        <option value="10" selected>10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                    <span>entries</span>
                </div>
                <div class="search-box">
                    <input type="text" class="table-search" placeholder="Search">
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <?php foreach ($columns as $column): ?>
                            <th><?php echo $column; ?></th>
                            <?php endforeach; ?>
                            <?php if ($actions_callback): ?>
                            <th>Actions</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $counter = 1;
                        foreach ($data as $row): 
                        ?>
                        <tr>
                            <td><?php echo $counter++; ?></td>
                            <?php foreach ($row as $key => $value): ?>
                                <?php if ($key !== 'actions'): ?>
                                <td><?php echo $value; ?></td>
                                <?php endif; ?>
                            <?php endforeach; ?>
                            <?php if ($actions_callback): ?>
                            <td><?php echo $actions_callback($row); ?></td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($data)): ?>
                        <tr>
                            <td colspan="<?php echo count($columns) + 2; ?>" class="text-center" style="padding: 2rem; color: var(--text-secondary);">
                                No data available
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="pagination-wrapper">
                <div class="pagination-info">
                    Showing 1 to <?php echo min(10, count($data)); ?> of <?php echo count($data); ?> entries
                </div>
                <ul class="pagination">
                    <li class="page-item disabled">
                        <a class="page-link" href="#">Previous</a>
                    </li>
                    <li class="page-item active">
                        <a class="page-link" href="#">1</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#">2</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#">Next</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <?php
}
?>
