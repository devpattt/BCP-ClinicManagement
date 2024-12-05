document.addEventListener("DOMContentLoaded", () => {
    const supplyTableBody = document.getElementById("supplyTableBody");

    const fetchSupplies = () => {
        fetch("fetchsupplies.php")
            .then((response) => response.json())
            .then((data) => {
                supplyTableBody.innerHTML = "";
                data.forEach((supply, index) => {
                    supplyTableBody.innerHTML += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${supply.item_name}</td>
                            <td>${supply.category || "N/A"}</td>
                            <td>${supply.quantity}</td>
                            <td>${supply.minimum_stock}</td>
                            <td>${supply.date_added}</td>
                            <td>
                                <button class="btn btn-sm btn-warning" onclick="editSupply(${supply.id}, '${supply.item_name}', '${supply.category}', ${supply.quantity}, ${supply.minimum_stock})">Edit</button>
                            </td>
                        </tr>
                    `;
                });
            });
    };

    fetchSupplies();

    const addSupplyForm = document.getElementById("addSupplyForm");
    addSupplyForm.addEventListener("submit", (e) => {
        e.preventDefault();
        const formData = new FormData(addSupplyForm);
        fetch("addsupplies.php", {
            method: "POST",
            body: formData,
        }).then(() => {
            fetchSupplies();
            addSupplyForm.reset();
            const addSupplyModal = bootstrap.Modal.getInstance(document.getElementById("addSupplyModal"));
            addSupplyModal.hide();
        });
    });

    window.editSupply = (id, itemName, category, quantity, minimumStock) => {
        document.getElementById("updateId").value = id;
        document.getElementById("updateItemName").value = itemName;
        document.getElementById("updateCategory").value = category;
        document.getElementById("updateQuantity").value = quantity;
        document.getElementById("updateMinimumStock").value = minimumStock;

        const updateSupplyModal = new bootstrap.Modal(document.getElementById("updateSupplyModal"));
        updateSupplyModal.show();
    };

    const updateSupplyForm = document.getElementById("updateSupplyForm");
    updateSupplyForm.addEventListener("submit", (e) => {
        e.preventDefault();
        const formData = new FormData(updateSupplyForm);
        fetch("updatesupplies.php", {
            method: "POST",
            body: formData,
        }).then(() => {
            fetchSupplies();
            const updateSupplyModal = bootstrap.Modal.getInstance(document.getElementById("updateSupplyModal"));
            updateSupplyModal.hide();
        });
    });
});
