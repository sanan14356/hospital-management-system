(function () {
    if (window.hmsAppLoaded) {
        return;
    }
    window.hmsAppLoaded = true;

    function ready(fn) {
        if (document.readyState !== "loading") {
            fn();
        } else {
            document.addEventListener("DOMContentLoaded", fn);
        }
    }

    function debounce(callback, wait) {
        var timeout;
        return function () {
            var context = this;
            var args = arguments;
            clearTimeout(timeout);
            timeout = setTimeout(function () {
                callback.apply(context, args);
            }, wait);
        };
    }

    function bindAjaxFilters() {
        var forms = document.querySelectorAll(".js-filter-form");
        forms.forEach(function (form) {
            var endpoint = form.dataset.endpoint;
            var targetId = form.dataset.target;
            if (!endpoint || !targetId) {
                return;
            }

            var target = document.getElementById(targetId);
            if (!target) {
                return;
            }

            var runSearch = function () {
                var params = new URLSearchParams(new FormData(form));
                fetch(endpoint + "?" + params.toString(), {
                    headers: {
                        "X-Requested-With": "XMLHttpRequest"
                    }
                })
                    .then(function (response) {
                        return response.json();
                    })
                    .then(function (data) {
                        if (data && typeof data.html !== "undefined") {
                            target.innerHTML = data.html;
                        }
                    })
                    .catch(function () {
                        // Keep existing rows if the request fails.
                    });
            };

            var schedule = debounce(runSearch, 300);
            form.addEventListener("input", schedule);
            form.addEventListener("change", schedule);
            form.addEventListener("submit", function (event) {
                event.preventDefault();
                runSearch();
            });
        });
    }

    function bindTicketInfo() {
        var select = document.getElementById("ticketPatient");
        var infoBox = document.getElementById("ticketPatientInfo");
        if (!select || !infoBox) {
            return;
        }

        var renderInfo = function (data) {
            if (data.error) {
                infoBox.innerHTML = '<div class="muted">' + data.error + '</div>';
                return;
            }

            infoBox.innerHTML =
                '<div><strong>Name:</strong> ' + data.full_name + '</div>' +
                '<div><strong>Phone:</strong> ' + data.phone + '</div>' +
                '<div><strong>Email:</strong> ' + data.email + '</div>' +
                '<div><strong>Disease:</strong> ' + data.disease + '</div>' +
                '<div><strong>Blood Group:</strong> ' + data.blood_group + '</div>' +
                '<div><strong>Admission:</strong> ' + data.admission_date + '</div>' +
                '<div><strong>Doctor:</strong> ' + data.doctor_name + '</div>' +
                '<div><strong>Specialization:</strong> ' + data.doctor_specialization + '</div>';
        };

        var loadPatient = function () {
            if (!select.value) {
                infoBox.innerHTML = '<div class="muted">Select a patient to view details.</div>';
                return;
            }

            fetch("ajax_patient_info.php?patient_id=" + encodeURIComponent(select.value))
                .then(function (response) {
                    return response.json();
                })
                .then(function (data) {
                    renderInfo(data);
                })
                .catch(function () {
                    infoBox.innerHTML = '<div class="muted">Unable to load patient details.</div>';
                });
        };

        select.addEventListener("change", loadPatient);
        if (select.value) {
            loadPatient();
        }
    }

    function bindFormValidation() {
        var forms = document.querySelectorAll('form[data-validate="true"]');
        forms.forEach(function (form) {
            form.addEventListener("submit", function (event) {
                var errors = [];
                var fields = form.querySelectorAll("[data-validate]");

                fields.forEach(function (field) {
                    var value = field.value.trim();
                    var rule = field.dataset.validate;

                    if (!value) {
                        return;
                    }

                    if (rule === "name") {
                        if (!/^[a-zA-Z\s'.-]+$/.test(value)) {
                            errors.push("Name cannot contain numbers or symbols.");
                        }
                    }

                    if (rule === "text") {
                        if (/\d/.test(value)) {
                            errors.push("This field cannot contain numbers.");
                        }
                    }

                    if (rule === "email") {
                        if (!/^\S+@\S+\.\S+$/.test(value)) {
                            errors.push("Please enter a valid email address.");
                        }
                    }

                    if (rule === "phone") {
                        if (!/^\+?[0-9]{7,15}$/.test(value)) {
                            errors.push("Phone number must contain 7 to 15 digits.");
                        }
                    }
                });

                var errorBox = form.querySelector(".form-errors");
                if (errors.length > 0) {
                    event.preventDefault();
                    if (errorBox) {
                        errorBox.classList.add("alert", "alert-error");
                        errorBox.innerHTML = errors.map(function (item) {
                            return "<div>" + item + "</div>";
                        }).join("");
                    }
                } else if (errorBox) {
                    errorBox.classList.remove("alert", "alert-error");
                    errorBox.innerHTML = "";
                }
            });
        });
    }

    ready(function () {
        var menuToggle = document.querySelector(".menu-toggle");
        if (menuToggle) {
            menuToggle.addEventListener("click", function () {
                document.body.classList.toggle("sidebar-open");
            });
        }

        document.querySelectorAll("[data-confirm]").forEach(function (link) {
            link.addEventListener("click", function (event) {
                var message = link.getAttribute("data-confirm") || "Are you sure?";
                if (!confirm(message)) {
                    event.preventDefault();
                }
            });
        });

        document.querySelectorAll(".alert").forEach(function (alert) {
            setTimeout(function () {
                alert.classList.add("fade-out");
            }, 3500);
        });

        bindAjaxFilters();
        bindTicketInfo();
        bindFormValidation();
    });
})();
