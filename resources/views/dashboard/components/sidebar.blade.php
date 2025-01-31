  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

      <li class="nav-item">
        <a class="nav-link collapsed" href="{{route(Auth::user()->data['type']==='Admin'?'dashboard.admin':'dashboard.teacher')}}">
          <i class="bi bi-grid"></i>
          <span>Dashboard</span>
        </a>
      </li><!-- End Dashboard Nav -->

      @if(Auth::user()->data['type'] === 'Admin')
      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#components-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-menu-button-wide"></i><span>Courses</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="components-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href={{route('addCourse')}}>
              <i class="bi bi-circle"></i><span>Add Course</span>
            </a>
          </li>
          <li>
            <a href={{route('showAllCourses')}}>
              <i class="bi bi-circle"></i><span>View Courses</span>
            </a>
          </li>
        </ul>
      </li><!-- End Components Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#components-nav2" data-bs-toggle="collapse" href="#">
          <i class="bi bi-person-badge"></i><span>Teachers</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="components-nav2" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href={{route('addTeacher')}}>
              <i class="bi bi-circle"></i><span>Add Teacher</span>
            </a>
          </li>
          <li>
            <a href={{route('showAllTeachers')}}>
              <i class="bi bi-circle"></i><span>View Teachers</span>
            </a>
          </li>
        </ul>
      </li><!-- End Components Nav -->
      @endif
      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#components-nav3" data-bs-toggle="collapse" href="#">
          <i class="bi bi-person-circle"></i><span>Students</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="components-nav3" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href={{route('addStudent')}}>
              <i class="bi bi-circle"></i><span>Add Student</span>
            </a>
          </li>
          <li>
            <a href={{route('showAllStudents')}}>
              <i class="bi bi-circle"></i><span>View Students</span>
            </a>
          </li>
        </ul>
      </li><!-- End Components Nav -->

    </ul>

  </aside><!-- End Sidebar-->
