document.addEventListener("DOMContentLoaded", function () {
    // Add User Form Submission
    document.getElementById("addUserForm").addEventListener("submit", function (e) {
        e.preventDefault();
        let formData = new FormData(this);

        fetch("/users", {
            method: "POST",
            body: formData,
            headers: {
                "X-CSRF-TOKEN": document.querySelector('input[name=_token]').value
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert("Có lỗi xảy ra!");
            }
        })
        .catch(error => console.error(error));
    });

    // Edit User
    document.querySelectorAll(".btn-edit").forEach(button => {
        button.addEventListener("click", function () {
            document.getElementById("editUserId").value = this.dataset.id;
            document.getElementById("editName").value = this.dataset.name;
            document.getElementById("editEmail").value = this.dataset.email;
            document.getElementById("editRole").value = this.dataset.role;
        });
    });

    document.getElementById("editUserForm").addEventListener("submit", function (e) {
        e.preventDefault();
        let userId = document.getElementById("editUserId").value;
        let formData = new FormData(this);

        fetch(`/users/${userId}`, {
            method: "PUT",
            body: formData,
            headers: {
                "X-CSRF-TOKEN": document.querySelector('input[name=_token]').value
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert("Có lỗi xảy ra!");
            }
        })
        .catch(error => console.error(error));
    });
});
