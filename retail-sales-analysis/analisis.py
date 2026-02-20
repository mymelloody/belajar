import pandas as pd
import matplotlib.pyplot as plt

# 1. DATA (Tetap dipertahankan)
data = {
    "Date": ["2026-02-01", "2026-02-01", "2026-02-02", "2026-02-03"],
    "Product": ["Anting", "Gelang", "Kalung", "Cincin"],
    "Quantity": [2, 1, 1, 3],
    "Price": [45000, 75000, 85000, 45000]
}
df = pd.DataFrame(data)
df["Total"] = df["Quantity"] * df["Price"]

# 2. ANALISIS REVENUE (Kode baru yang kamu kirim tadi)
# Ini menghitung total uang yang masuk untuk setiap produk
revenue_product = df.groupby("Product")["Total"].sum().sort_values(ascending=False)

# 3. TAMPILKAN HASIL
print("--- LAPORAN PENJUALAN ---")
print(df)
print("\nRevenue per Produk (Uang Masuk):")
print(revenue_product)

# 4. GRAFIK (Tetap di paling bawah)
revenue_product.plot(kind='bar', color='lightgreen', edgecolor='black')
plt.title('Total Pendapatan per Produk')
plt.ylabel('Rupiah')
plt.show()

# Insight sederhana otomatis
best_product = revenue_product.idxmax()
highest_revenue = revenue_product.max()

print("\nBusiness Insight:")
print(f"Produk dengan revenue tertinggi adalah {best_product} dengan total Rp {highest_revenue}")