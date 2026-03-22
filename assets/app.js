const productSelect = document.getElementById("productSku");
const productList = document.getElementById("productList");
const messageBox = document.getElementById("message");
const orderForm = document.getElementById("orderForm");

function showMessage(text, type = "info") {
    messageBox.textContent = text;
    messageBox.className = type;
}

async function loadProducts () {
    try {
        const response = await fetch('erp/products.php');
        const data = await response.json();

        if (!data.success) {
            showMessage(data.message || "Failed to load products", "error");
            return;
        }

        productSelect.innerHTML = '<option value="">Select product</option>';
        productList.innerHTML = "";

        data.products.forEach((product) => {
            const option = document.createElement("option");
            option.value = product.sku;
            option.textContent = `${product.name} (${product.sku})`;
            productSelect.appendChild(option);

            const productItem = document.createElement("div");
            productItem.innerHTML = `
            <strong>${product.name}</strong><br>
            SKU: ${product.sku}<br>
            Stock: ${product.stock}<br>
            Price: €${product.price}
            `;

            productList.appendChild(productItem);
            productList.appendChild(document.createElement("hr"));
        });
    } catch (error) {
        console.error(error.message);
        showMessage("Error loading products", "error");
    }
}

loadProducts();

orderForm.addEventListener('submit', (e) => {
    e.preventDefault();

    // Get customer name
    const customerName = document.getElementById('customerName').value.trim();

    // Get product label
    const productSku = productSelect.value;

    // Get quantity
    const quantity = parseInt(document.getElementById('quantity').value, 10);

    const payload = {
        customerName: customerName,
        productSku: productSku,
        quantity: quantity
    };

    console.log(payload);
});