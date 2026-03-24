<!-- Training Material Management -->
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/enpharchem/dashboard" class="text-decoration-none" style="color: var(--epc-accent);">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/enpharchem/control-panel" class="text-decoration-none" style="color: var(--epc-accent);">Control Panel</a></li>
        <li class="breadcrumb-item active text-light" aria-current="page">Training Material</li>
    </ol>
</nav>

<h2 class="text-light mb-4"><i class="bi bi-mortarboard-fill me-2" style="color: var(--epc-accent);"></i>Training Material</h2>

<!-- Seed & Public Training Links -->
<div class="card border-0 mb-4" style="background: linear-gradient(135deg, rgba(13,110,253,0.15), rgba(13,202,240,0.10)); border: 1px solid rgba(13,202,240,0.2) !important;">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-7">
                <h5 class="text-light mb-2"><i class="bi bi-database-fill-gear me-2 text-info"></i>Seed Complete Training Program</h5>
                <p class="text-secondary mb-0 small">Load <strong class="text-light">60 courses</strong> (4 levels x 15 categories), <strong class="text-light">300+ lessons</strong>, and <strong class="text-light">600+ assessment questions</strong> with realistic engineering content. Includes automatic certificate generation for passing scores.</p>
            </div>
            <div class="col-md-5 text-md-end mt-3 mt-md-0">
                <form method="POST" action="/enpharchem/training/seed" class="d-inline">
                    <button type="submit" class="btn btn-success me-2" onclick="this.innerHTML='<i class=\'bi bi-hourglass-split me-1\'></i>Seeding...'; this.disabled=true; this.form.submit();">
                        <i class="bi bi-database-fill-gear me-1"></i>Seed Training Data
                    </button>
                </form>
                <a href="/enpharchem/training" class="btn btn-outline-info">
                    <i class="bi bi-box-arrow-up-right me-1"></i>View Public Training
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Stats Row -->
<div class="row g-3 mb-4">
    <div class="col-md-3 col-sm-6">
        <div class="card border-0" style="background: var(--epc-card-bg);">
            <div class="card-body text-center">
                <i class="bi bi-book-fill text-primary fs-4"></i>
                <div class="text-light fs-3 fw-bold"><?= number_format($trainingStats['total_courses'] ?? 0) ?></div>
                <div class="text-secondary small">Total Courses</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card border-0" style="background: var(--epc-card-bg);">
            <div class="card-body text-center">
                <i class="bi bi-check-circle-fill text-success fs-4"></i>
                <div class="text-light fs-3 fw-bold"><?= number_format($trainingStats['active_courses'] ?? 0) ?></div>
                <div class="text-secondary small">Active Courses</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card border-0" style="background: var(--epc-card-bg);">
            <div class="card-body text-center">
                <i class="bi bi-journal-text text-info fs-4"></i>
                <div class="text-light fs-3 fw-bold"><?= number_format($trainingStats['total_lessons'] ?? 0) ?></div>
                <div class="text-secondary small">Total Lessons</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card border-0" style="background: var(--epc-card-bg);">
            <div class="card-body text-center">
                <i class="bi bi-people-fill text-warning fs-4"></i>
                <div class="text-light fs-3 fw-bold"><?= number_format($trainingStats['total_enrollments'] ?? 0) ?></div>
                <div class="text-secondary small">Total Enrollments</div>
            </div>
        </div>
    </div>
</div>

<!-- Courses Section -->
<div class="card border-0 mb-4" style="background: var(--epc-card-bg);">
    <div class="card-header d-flex justify-content-between align-items-center border-bottom border-secondary" style="background: var(--epc-card-bg);">
        <h5 class="text-light mb-0"><i class="bi bi-book-fill me-2 text-primary"></i>Courses</h5>
        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#createCourseModal">
            <i class="bi bi-plus-lg me-1"></i>Create Course
        </button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-dark table-hover mb-0" style="background: transparent;">
                <thead>
                    <tr class="text-secondary small text-uppercase">
                        <th>Title</th>
                        <th>Category</th>
                        <th>Level</th>
                        <th>Duration</th>
                        <th>Lessons</th>
                        <th>Enrollments</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($courses)): ?>
                        <?php
                        $levelColors = [
                            'beginner' => 'bg-success',
                            'intermediate' => 'bg-primary',
                            'advanced' => 'bg-warning text-dark',
                            'expert' => 'bg-danger',
                        ];
                        $catBadgeColors = [
                            'process_simulation' => 'bg-info',
                            'exchanger_design' => 'bg-primary',
                            'apc' => 'bg-success',
                            'mes' => 'bg-warning text-dark',
                            'supply_chain' => 'bg-secondary',
                            'apm' => 'bg-danger',
                            'grid_mgmt' => 'bg-dark border border-secondary',
                            'general' => 'bg-secondary',
                        ];
                        ?>
                        <?php foreach ($courses as $course): ?>
                            <tr>
                                <td class="text-light fw-semibold"><?= htmlspecialchars($course['title'] ?? '') ?></td>
                                <td>
                                    <?php $cCat = $course['category'] ?? 'general'; ?>
                                    <span class="badge <?= $catBadgeColors[$cCat] ?? 'bg-secondary' ?>"><?= ucwords(str_replace('_', ' ', $cCat)) ?></span>
                                </td>
                                <td>
                                    <?php $cLevel = $course['level'] ?? 'beginner'; ?>
                                    <span class="badge <?= $levelColors[$cLevel] ?? 'bg-secondary' ?>"><?= ucfirst($cLevel) ?></span>
                                </td>
                                <td class="text-light"><?= htmlspecialchars($course['duration_hours'] ?? '0') ?>h</td>
                                <td class="text-light"><?= number_format($lessonCountMap[$course['id']] ?? 0) ?></td>
                                <td class="text-light"><?= number_format($course['enrollment_count'] ?? 0) ?></td>
                                <td>
                                    <?php
                                    $cStatus = $course['status'] ?? 'draft';
                                    $cStatusBadge = $cStatus === 'active' ? 'bg-success' : ($cStatus === 'draft' ? 'bg-warning text-dark' : 'bg-secondary');
                                    ?>
                                    <span class="badge <?= $cStatusBadge ?>"><?= ucfirst($cStatus) ?></span>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="action" value="toggle_course">
                                            <input type="hidden" name="course_id" value="<?= htmlspecialchars($course['id'] ?? '') ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-warning" title="Toggle Status">
                                                <i class="bi bi-arrow-repeat"></i>
                                            </button>
                                        </form>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="action" value="delete_course">
                                            <input type="hidden" name="course_id" value="<?= htmlspecialchars($course['id'] ?? '') ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete" onclick="return confirm('Delete this course?')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="8" class="text-center text-secondary py-4">No courses found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create Course Modal -->
<div class="modal fade" id="createCourseModal" tabindex="-1" aria-labelledby="createCourseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background: var(--epc-card-bg); border: 1px solid rgba(255,255,255,0.1);">
            <div class="modal-header border-bottom border-secondary">
                <h5 class="modal-title text-light" id="createCourseModalLabel"><i class="bi bi-plus-circle me-2 text-primary"></i>Create Course</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="create_course">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="courseTitle" class="form-label text-light">Title</label>
                            <input type="text" class="form-control bg-dark text-light border-secondary" id="courseTitle" name="title" required>
                        </div>
                        <div class="col-12">
                            <label for="courseDesc" class="form-label text-light">Description</label>
                            <textarea class="form-control bg-dark text-light border-secondary" id="courseDesc" name="description" rows="3"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label for="courseCategory" class="form-label text-light">Category</label>
                            <select class="form-select bg-dark text-light border-secondary" id="courseCategory" name="category" required>
                                <option value="process_simulation">Process Simulation</option>
                                <option value="exchanger_design">Exchanger Design</option>
                                <option value="apc">APC</option>
                                <option value="mes">MES</option>
                                <option value="supply_chain">Supply Chain</option>
                                <option value="apm">APM</option>
                                <option value="grid_mgmt">Grid Management</option>
                                <option value="general">General</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="courseLevel" class="form-label text-light">Level</label>
                            <select class="form-select bg-dark text-light border-secondary" id="courseLevel" name="level" required>
                                <option value="beginner">Beginner</option>
                                <option value="intermediate">Intermediate</option>
                                <option value="advanced">Advanced</option>
                                <option value="expert">Expert</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="courseDuration" class="form-label text-light">Duration (hours)</label>
                            <input type="number" class="form-control bg-dark text-light border-secondary" id="courseDuration" name="duration_hours" min="0" step="0.5">
                        </div>
                        <div class="col-md-6">
                            <label for="courseInstructor" class="form-label text-light">Instructor</label>
                            <input type="text" class="form-control bg-dark text-light border-secondary" id="courseInstructor" name="instructor">
                        </div>
                        <div class="col-12">
                            <label for="coursePrereqs" class="form-label text-light">Prerequisites</label>
                            <textarea class="form-control bg-dark text-light border-secondary" id="coursePrereqs" name="prerequisites" rows="2" placeholder="List any prerequisites..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top border-secondary">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Course</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Lessons Section -->
<div class="card border-0 mb-4" style="background: var(--epc-card-bg);">
    <div class="card-header d-flex justify-content-between align-items-center border-bottom border-secondary" style="background: var(--epc-card-bg);">
        <h5 class="text-light mb-0"><i class="bi bi-journal-text me-2 text-info"></i>Lessons</h5>
        <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#createLessonModal">
            <i class="bi bi-plus-lg me-1"></i>Create Lesson
        </button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-dark table-hover mb-0" style="background: transparent;">
                <thead>
                    <tr class="text-secondary small text-uppercase">
                        <th>Title</th>
                        <th>Course</th>
                        <th>Order</th>
                        <th>Type</th>
                        <th>Duration</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($lessons)): ?>
                        <?php
                        $lessonTypeColors = [
                            'video' => 'bg-danger',
                            'document' => 'bg-primary',
                            'quiz' => 'bg-warning text-dark',
                            'lab' => 'bg-success',
                            'interactive' => 'bg-info',
                        ];
                        ?>
                        <?php foreach ($lessons as $lesson): ?>
                            <tr>
                                <td class="text-light fw-semibold"><?= htmlspecialchars($lesson['title'] ?? '') ?></td>
                                <td class="text-secondary"><?= htmlspecialchars($lesson['course_title'] ?? '') ?></td>
                                <td class="text-light"><?= htmlspecialchars($lesson['lesson_order'] ?? '') ?></td>
                                <td>
                                    <?php $lType = $lesson['lesson_type'] ?? 'document'; ?>
                                    <span class="badge <?= $lessonTypeColors[$lType] ?? 'bg-secondary' ?>"><?= ucfirst($lType) ?></span>
                                </td>
                                <td class="text-light"><?= htmlspecialchars($lesson['duration_minutes'] ?? '0') ?> min</td>
                                <td>
                                    <?php
                                    $lStatus = $lesson['status'] ?? 'draft';
                                    $lStatusBadge = $lStatus === 'active' ? 'bg-success' : ($lStatus === 'draft' ? 'bg-warning text-dark' : ($lStatus === 'published' ? 'bg-success' : 'bg-secondary'));
                                    ?>
                                    <span class="badge <?= $lStatusBadge ?>"><?= ucfirst($lStatus) ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center text-secondary py-4">No lessons found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create Lesson Modal -->
<div class="modal fade" id="createLessonModal" tabindex="-1" aria-labelledby="createLessonModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="background: var(--epc-card-bg); border: 1px solid rgba(255,255,255,0.1);">
            <div class="modal-header border-bottom border-secondary">
                <h5 class="modal-title text-light" id="createLessonModalLabel"><i class="bi bi-plus-circle me-2 text-info"></i>Create Lesson</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="create_lesson">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="lessonCourse" class="form-label text-light">Course</label>
                        <select class="form-select bg-dark text-light border-secondary" id="lessonCourse" name="course_id" required>
                            <option value="">-- Select Course --</option>
                            <?php foreach ($courses as $course): ?>
                                <option value="<?= htmlspecialchars($course['id'] ?? '') ?>"><?= htmlspecialchars($course['title'] ?? '') ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="lessonTitle" class="form-label text-light">Title</label>
                        <input type="text" class="form-control bg-dark text-light border-secondary" id="lessonTitle" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="lessonDesc" class="form-label text-light">Description</label>
                        <textarea class="form-control bg-dark text-light border-secondary" id="lessonDesc" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="lessonType" class="form-label text-light">Lesson Type</label>
                        <select class="form-select bg-dark text-light border-secondary" id="lessonType" name="lesson_type" required>
                            <option value="video">Video</option>
                            <option value="document">Document</option>
                            <option value="quiz">Quiz</option>
                            <option value="lab">Lab</option>
                            <option value="interactive">Interactive</option>
                        </select>
                    </div>
                    <div class="row g-3">
                        <div class="col-6">
                            <label for="lessonDuration" class="form-label text-light">Duration (minutes)</label>
                            <input type="number" class="form-control bg-dark text-light border-secondary" id="lessonDuration" name="duration_minutes" min="0">
                        </div>
                        <div class="col-6">
                            <label for="lessonOrder" class="form-label text-light">Lesson Order</label>
                            <input type="number" class="form-control bg-dark text-light border-secondary" id="lessonOrder" name="lesson_order" min="1" value="1">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top border-secondary">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info">Create Lesson</button>
                </div>
            </form>
        </div>
    </div>
</div>
