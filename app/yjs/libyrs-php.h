#define FFI_LIB "/www/tpdev/app/yjs/libyrs.so"
typedef struct YDoc {} YDoc;
typedef struct Branch {} Branch;
typedef struct TransactionInner TransactionInner;
typedef struct TransactionInner YTransaction;

typedef struct YOptions {
    /**
        * Globally unique 53-bit integer assigned to corresponding document replica as its identifier.
        *
        * If two clients share the same `id` and will perform any updates, it will result in
        * unrecoverable document state corruption. The same thing may happen if the client restored
        * document state from snapshot, that didn't contain all of that clients updates that were sent
        * to other peers.
        */
    uint64_t id;
    /**
        * A NULL-able globally unique Uuid v4 compatible null-terminated string identifier
        * of this document. If passed as NULL, a random Uuid will be generated instead.
        */
    const char *guid;
    /**
        * A NULL-able, UTF-8 encoded, null-terminated string of a collection that this document
        * belongs to. It's used only by providers.
        */
    const char *collection_id;
    /**
        * Encoding used by text editing operations on this document. It's used to compute
        * `YText`/`YXmlText` insertion offsets and text lengths. Either:
        *
        * - `Y_OFFSET_BYTES`
        * - `Y_OFFSET_UTF16`
        */
    uint8_t encoding;
    /**
        * Boolean flag used to determine if deleted blocks should be garbage collected or not
        * during the transaction commits. Setting this value to 0 means GC will be performed.
        */
    uint8_t skip_gc;
    /**
        * Boolean flag used to determine if subdocument should be loaded automatically.
        * If this is a subdocument, remote peers will load the document as well automatically.
        */
    uint8_t auto_load;
    /**
        * Boolean flag used to determine whether the document should be synced by the provider now.
        */
    uint8_t should_load;
    } YOptions;


YDoc *ydoc_new(void);

Branch *ytext(YDoc *doc, const char *name);

YTransaction *ydoc_write_transaction(YDoc *doc, uint32_t origin_len, const char *origin);

void ytext_insert(const Branch *txt,
    YTransaction *txn,
    uint32_t index,
    const char *value,
    const struct YInput *attrs);

void ytransaction_commit(YTransaction *txn);

char *ytext_string(const Branch *txt, const YTransaction *txn);

char *ytransaction_snapshot(const YTransaction *txn, uint32_t *len);

char *ytransaction_encode_state_from_snapshot_v1(const YTransaction *txn,
    const char *snapshot,
    uint32_t snapshot_len,
    uint32_t *len);

char *ytransaction_encode_state_from_snapshot_v2(const YTransaction *txn,
    const char *snapshot,
    uint32_t snapshot_len,
    uint32_t *len);

uint8_t ytransaction_apply(YTransaction *txn,
    const char *diff,
    uint32_t diff_len);

YDoc *ydoc_new_with_options(struct YOptions options);

char *ytransaction_state_diff_v1(const YTransaction *txn,
    const char *sv,
    uint32_t sv_len,
    uint32_t *len);

char *ytransaction_state_vector_v1(const YTransaction *txn, uint32_t *len);

void ydoc_destroy(YDoc *value);

void ybinary_destroy(char *ptr, uint32_t len);
